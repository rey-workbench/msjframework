<?php

namespace MSJFramework\LaravelGenerator\Console;

use Illuminate\Contracts\Foundation\Application;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\Prompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\TextPrompt;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class WindowsFallbackConfigurator
{
    public function __construct(
        private Application $app
    ) {}

    /**
     * Detect if running on Windows native (not WSL).
     */
    public static function isWindowsNative(): bool
    {
        // macOS and Linux always return false
        if (PHP_OS_FAMILY !== 'Windows') {
            return false;
        }

        // Check if running in WSL (Windows Subsystem for Linux)
        if (getenv('WSL_DISTRO_NAME') !== false || getenv('WSL_INTEROP') !== false) {
            return false;
        }

        // Additional check: WSL typically has '/proc/version' containing 'microsoft'
        if (is_readable('/proc/version')) {
            $version = file_get_contents('/proc/version');
            if (stripos($version, 'microsoft') !== false || stripos($version, 'WSL') !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Configure Laravel Prompts to fallback on Windows native.
     */
    public function configure(): void
    {
        // Set fallback condition
        Prompt::fallbackWhen(
            ! $this->app->runningInConsole() || 
            self::isWindowsNative() || 
            $this->app->runningUnitTests()
        );

        // Configure specific fallbacks for Windows native
        if (self::isWindowsNative()) {
            $this->configurePromptFallbacks();
            $this->disableOutputDecoration();
        }
    }

    /**
     * Configure fallback behavior for each prompt type.
     */
    private function configurePromptFallbacks(): void
    {
        TextPrompt::fallbackUsing(fn (TextPrompt $prompt) => 
            $this->getQuestionHelper()->ask(
                $this->getInput(),
                $this->getOutput(),
                new Question($prompt->label . ' ', $prompt->default ?? null)
            )
        );

        ConfirmPrompt::fallbackUsing(fn (ConfirmPrompt $prompt) => 
            $this->getQuestionHelper()->ask(
                $this->getInput(),
                $this->getOutput(),
                new \Symfony\Component\Console\Question\ConfirmationQuestion(
                    $prompt->label . ' (yes/no) ',
                    $prompt->default ?? false
                )
            )
        );

        SelectPrompt::fallbackUsing(function (SelectPrompt $prompt) {
            $choices = [];
            foreach ($prompt->options as $key => $value) {
                $choices[$key] = is_array($value) ? ($value['label'] ?? $key) : $value;
            }

            $question = new ChoiceQuestion($prompt->label . ' ', $choices, $prompt->default ?? null);
            $question->setErrorMessage('Selection %s is invalid.');

            return $this->getQuestionHelper()->ask(
                $this->getInput(),
                $this->getOutput(),
                $question
            );
        });

        MultiSelectPrompt::fallbackUsing(function (MultiSelectPrompt $prompt) {
            $choices = [];
            foreach ($prompt->options as $key => $value) {
                $choices[$key] = is_array($value) ? ($value['label'] ?? $key) : $value;
            }

            $question = new ChoiceQuestion(
                $prompt->label . ' (comma-separated for multiple) ',
                $choices
            );
            $question->setMultiselect(true);
            $question->setErrorMessage('Selection %s is invalid.');

            if ($prompt->required ?? false) {
                $question->setValidator(function ($answer) {
                    if (empty($answer)) {
                        throw new \RuntimeException('At least one option must be selected.');
                    }
                    return $answer;
                });
            }

            return $this->getQuestionHelper()->ask(
                $this->getInput(),
                $this->getOutput(),
                $question
            );
        });
    }

    /**
     * Disable output decoration to prevent style tag errors.
     */
    private function disableOutputDecoration(): void
    {
        if ($this->app->runningInConsole()) {
            try {
                $output = $this->getOutput();
                if ($output && method_exists($output, 'getFormatter')) {
                    $formatter = $output->getFormatter();
                    if ($formatter && method_exists($formatter, 'setDecorated')) {
                        $formatter->setDecorated(false);
                    }
                }
            } catch (\Exception $e) {
                // Silently fail if output formatter is not available
            }
        }
    }

    private function getQuestionHelper()
    {
        return $this->app['Symfony\Component\Console\Helper\QuestionHelper'] 
            ?? new \Symfony\Component\Console\Helper\QuestionHelper();
    }

    private function getInput()
    {
        if ($this->app->bound('Symfony\Component\Console\Input\InputInterface')) {
            return $this->app['Symfony\Component\Console\Input\InputInterface'];
        }
        
        return new \Symfony\Component\Console\Input\ArgvInput();
    }

    private function getOutput()
    {
        if ($this->app->bound('Symfony\Component\Console\Output\OutputInterface')) {
            $output = $this->app['Symfony\Component\Console\Output\OutputInterface'];
        } else {
            // Create ConsoleOutput with decoration disabled for Windows
            $output = new \Symfony\Component\Console\Output\ConsoleOutput(
                \Symfony\Component\Console\Output\ConsoleOutput::VERBOSITY_NORMAL,
                false
            );
        }
        
        // Ensure decoration is disabled on Windows native
        if (self::isWindowsNative() && method_exists($output, 'getFormatter')) {
            $formatter = $output->getFormatter();
            if ($formatter && method_exists($formatter, 'setDecorated')) {
                $formatter->setDecorated(false);
            }
        }
        
        return $output;
    }
}

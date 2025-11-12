<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

use MSJFramework\LaravelGenerator\Support\CrossPlatformPrompt;

trait HasConsoleStyling
{
    /**
     * Check if running on Windows native.
     */
    protected function isWindowsNative(): bool
    {
        return CrossPlatformPrompt::isWindowsNative();
    }

    /**
     * Strip style tags if on Windows.
     */
    protected function stripStyleTags(string $text): string
    {
        return CrossPlatformPrompt::stripStyleTags($text);
    }

    protected function badge(string $type, string $message): void
    {
        $styles = [
            'success' => ['bg' => 'green', 'fg' => 'white', 'text' => 'SUCCESS', 'plain' => '[SUCCESS]'],
            'error' => ['bg' => 'red', 'fg' => 'white', 'text' => 'ERROR', 'plain' => '[ERROR]'],
            'warning' => ['bg' => 'yellow', 'fg' => 'black', 'text' => 'WARNING', 'plain' => '[WARNING]'],
            'completed' => ['bg' => 'cyan', 'fg' => 'white', 'text' => 'COMPLETED', 'plain' => '[COMPLETED]'],
            'info' => ['bg' => 'blue', 'fg' => 'white', 'text' => 'INFO', 'plain' => '[INFO]'],
        ];

        $style = $styles[$type] ?? $styles['info'];
        
        if ($this->isWindowsNative()) {
            // Windows: use plain text badges without style tags
            $this->line("{$style['plain']} {$message}");
        } else {
            // Linux/macOS/WSL: use styled badges
            $badge = "<bg={$style['bg']};fg={$style['fg']}> {$style['text']} </>";
            $this->line("{$badge} {$message}");
        }
    }

    protected function displayHeader(string $title): void
    {
        $this->newLine();
        
        if ($this->isWindowsNative()) {
            // Windows: plain ASCII art without colors
            $this->line('         &&&&&&&&&&&&&&&&&&');
            $this->line('     &&&&&&&&&&&&  &&&&&&&&&&&&        _ __ ___  ___ (_)');
            $this->line('   &&&&&&&&&&&&&&  &&&&&&&&&&&&&&     | \'_ ` _ \\/ __|| |');
            $this->line(' &&&&&&&&&&&& &&    && &&&&&&&&&&&    | | | | | \\__ \\| |');
            $this->line('&&&&&&&&& &&   & && &   && &&&&&&&&&  |_|_|_| |_|___// |                               _');
            $this->line('&&&&&&&&&  & &  &&&   & &  &&&&&&&&&   / _|_ __ __ |__/__ ___   _____      _____  _ __| | __');
            $this->line('&&&&&&&& &  &&  &&&&  &&  & &&&&&&&&  | |_| \'__/ _` | \'_ ` _ \\ / _ \\ \\ /\\ / / _ \\| \'__| |/ /');
            $this->line('&&&&&&& &&  & && && && &  && &&&&&&&  |  _| | | (_| | | | | | |  __/\\ V  V / (_) | |  |   <');
            $this->line(' &&&&& &  &  &&&&  &&&&  &  & &&&&&   |_| |_|  \\__,_|_| |_| |_|\\___| \\_/\\_/ \\___/|_|  |_|\\_\\');
            $this->line('   &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&');
            $this->line('     &&&&&&&&&&&&&&&&&&&&&&&&&&');
            $this->line('         &&&&&&&&&&&&&&&&&&');
            $this->newLine();
            $this->line("* {$title}");
        } else {
            // Linux/macOS/WSL: colored ASCII art
            $this->line('<fg=cyan>         &&&&&&&&&&&&&&&&&&                                                </>');
            $this->line('<fg=cyan>     &&&&&&&&&&&&  &&&&&&&&&&&&        _ __ ___  ___ (_)                                    </>');
            $this->line('<fg=cyan>   &&&&&&&&&&&&&&  &&&&&&&&&&&&&&     | \'_ ` _ \\/ __|| |                                    </>');
            $this->line('<fg=cyan> &&&&&&&&&&&& &&    && &&&&&&&&&&&    | | | | | \\__ \\| |                                    </>');
            $this->line('<fg=cyan>&&&&&&&&& &&   & && &   && &&&&&&&&&  |_|_|_| |_|___// |                               _    </>');
            $this->line('<fg=cyan>&&&&&&&&&  & &  &&&   & &  &&&&&&&&&   / _|_ __ __ |__/__ ___   _____      _____  _ __| | __</>');
            $this->line('<fg=cyan>&&&&&&&& &  &&  &&&&  &&  & &&&&&&&&  | |_| \'__/ _` | \'_ ` _ \\ / _ \\ \\ /\\ / / _ \\| \'__| |/ /</>');
            $this->line('<fg=cyan>&&&&&&& &&  & && && && &  && &&&&&&&  |  _| | | (_| | | | | | |  __/\\ V  V / (_) | |  |   < </>');
            $this->line('<fg=cyan> &&&&& &  &  &&&&  &&&&  &  & &&&&&   |_| |_|  \\__,_|_| |_| |_|\\___| \\_/\\_/ \\___/|_|  |_|\\_\\</>');
            $this->line('<fg=cyan>   &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&     </>');
            $this->line('<fg=cyan>     &&&&&&&&&&&&&&&&&&&&&&&&&&       </>');
            $this->line('<fg=cyan>         &&&&&&&&&&&&&&&&&&           </>');
            $this->newLine();
            $this->components->bulletList([
                "<fg=bright-cyan>{$title}</>",
            ]);
        }

        $this->newLine();
    }

    protected function section(string $title): void
    {
        $this->newLine();

        if ($this->isWindowsNative()) {
            // Windows: plain text section
            $this->line("==> {$title}");
        } else {
            // Linux/macOS/WSL: styled section
            $this->components->task($title);
        }
    }

    /**
     * Output a line, automatically stripping style tags on Windows.
     */
    protected function line($string, $style = null, $verbosity = null): void
    {
        if ($this->isWindowsNative()) {
            // Strip all style tags for Windows
            $string = $this->stripStyleTags($string);
        }
        
        parent::line($string, $style, $verbosity);
    }
}

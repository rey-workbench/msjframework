<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

trait HasConsoleStyling
{
    protected function badge(string $type, string $message): void
    {
        $styles = [
            'success' => ['bg' => 'green', 'fg' => 'white', 'text' => 'SUCCESS'],
            'error' => ['bg' => 'red', 'fg' => 'white', 'text' => 'ERROR'],
            'warning' => ['bg' => 'yellow', 'fg' => 'black', 'text' => 'WARNING'],
            'completed' => ['bg' => 'cyan', 'fg' => 'white', 'text' => 'COMPLETED'],
            'info' => ['bg' => 'blue', 'fg' => 'white', 'text' => 'INFO'],
        ];

        $style = $styles[$type] ?? $styles['info'];
        $badge = "<bg={$style['bg']};fg={$style['fg']}> {$style['text']} </>";

        $this->line("{$badge} {$message}");
    }

    protected function displayHeader(string $title): void
    {
        $this->newLine();
        $this->line('<fg=cyan>         &&&&&&&&&&&&&&&&&&                                                </>');
        $this->line('<fg=cyan>     &&&&&&&&&&&&  &&&&&&&&&&&&        _ __ ___  ___ (_)                                    </>');
        $this->line('<fg=cyan>   &&&&&&&&&&&&&&  &&&&&&&&&&&&&&     | \'_ ` _ \\/ __|| |                                    </>');
        $this->line('<fg=cyan> &&&&&&&&&&&& &&    && &&&&&&&&&&&    | | | | | \\__ \\| |                                    </>');
        $this->line('<fg=cyan>&&&&&&&&& &&   & && &   && &&&&&&&&&  |_|_|_| |_|___// |                               _    </>');
        $this->line('<fg=cyan>&&&&&&&&&  & &  &&&   & &  &&&&&&&&&   / _|_ __ __ |__/__ ___   _____      _____  _ __| | __</>');
        $this->line('<fg=cyan>&&&&&&&& &  &&  &&&&  &&  & &&&&&&&&  | |_| \'__/ _` | \'_ ` _ \\ / _ \\ \\ /\\ / / _ \\| \'__| |/ /</>');
        $this->line('<fg=cyan>&&&&&&& &&  & && && && &  && &&&&&&&  |  _| | | (_| | | | | | |  __/\\ V  V / (_) | |  |   < </>');
        $this->line('<fg=cyan> &&&&& &  &  &&&&  &&&&  &  & &&&&&   |_| |_|  \\__,_|_| |_| |_|\\___| \\_/\\_/ \\___/|_|  |_|\\_\\');
        $this->line('<fg=cyan>   &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&     </>');
        $this->line('<fg=cyan>     &&&&&&&&&&&&&&&&&&&&&&&&&&       </>');
        $this->line('<fg=cyan>         &&&&&&&&&&&&&&&&&&           </>');
        $this->newLine();

        $this->newLine();

        $this->components->bulletList([
            "<fg=bright-cyan;options=bold>{$title}</>",
        ]);

        $this->newLine();
    }

    protected function section(string $title): void
    {
        $this->newLine();

        $this->components->task($title);
    }
}

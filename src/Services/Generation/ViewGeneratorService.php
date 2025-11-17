<?php

namespace MSJFramework\LaravelGenerator\Services\Generation;

use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Master\AddView as MasterAddView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Master\EditView as MasterEditView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Master\ListView as MasterListView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Master\ShowView as MasterShowView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Report\FilterView as ReportFilterView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Report\ResultView as ReportResultView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Standr\AddView as StandrAddView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Standr\EditView as StandrEditView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Standr\ListView as StandrListView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Standr\ShowView as StandrShowView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Sublnk\AddView as SublnkAddView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Sublnk\EditView as SublnkEditView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Sublnk\ListView as SublnkListView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Sublnk\ShowView as SublnkShowView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\System\AddView as SystemAddView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\System\EditView as SystemEditView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\System\ListView as SystemListView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\System\ShowView as SystemShowView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Transc\AddView as TranscAddView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Transc\EditView as TranscEditView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Transc\ListView as TranscListView;
use MSJFramework\LaravelGenerator\Templates\Views\Layouts\Transc\ShowView as TranscShowView;
use MSJFramework\LaravelGenerator\Templates\Views\Manual\AddView;
use MSJFramework\LaravelGenerator\Templates\Views\Manual\EditView;
use MSJFramework\LaravelGenerator\Templates\Views\Manual\ListView;
use MSJFramework\LaravelGenerator\Templates\Views\Manual\ShowView;
use Illuminate\Support\Facades\File;

class ViewGeneratorService
{
    public function generate(array $config): array
    {
        $viewsPath = resource_path("views/{$config['gmenu']}/{$config['url']}");

        // Create directory
        if (! File::exists($viewsPath)) {
            File::makeDirectory($viewsPath, 0755, true);
        }

        $results = [];

        if ($config['layout'] === 'report') {
            $results[] = $this->generateReportViews($config, $viewsPath);
        } else {
            $results[] = $this->generateStandardViews($config, $viewsPath);
        }

        return [
            'status' => 'success',
            'message' => 'Views berhasil dibuat',
            'path' => $viewsPath,
            'details' => $results,
        ];
    }

    protected function generateStandardViews(array $config, string $basePath): array
    {
        $layout = $config['layout'];
        $files = [];

        // List view
        $listContent = $this->getListViewTemplate($layout, $config);
        File::put("{$basePath}/list.blade.php", $listContent);
        $files[] = 'list.blade.php';

        // Add view
        $addContent = $this->getAddViewTemplate($layout, $config);
        File::put("{$basePath}/add.blade.php", $addContent);
        $files[] = 'add.blade.php';

        // Edit view
        $editContent = $this->getEditViewTemplate($layout, $config);
        File::put("{$basePath}/edit.blade.php", $editContent);
        $files[] = 'edit.blade.php';

        // Show view
        $showContent = $this->getShowViewTemplate($layout, $config);
        File::put("{$basePath}/show.blade.php", $showContent);
        $files[] = 'show.blade.php';

        return $files;
    }

    protected function generateReportViews(array $config, string $basePath): array
    {
        $files = [];

        // Filter view
        $filterContent = ReportFilterView::getTemplate($config);
        File::put("{$basePath}/filter.blade.php", $filterContent);
        $files[] = 'filter.blade.php';

        // Result view
        $resultContent = ReportResultView::getTemplate($config);
        File::put("{$basePath}/result.blade.php", $resultContent);
        $files[] = 'result.blade.php';

        return $files;
    }

    protected function getListViewTemplate(string $layout, array $config): string
    {
        return match ($layout) {
            'manual' => ListView::getTemplate($config),
            'standr' => StandrListView::getTemplate($config),
            'master' => MasterListView::getTemplate($config),
            'transc' => TranscListView::getTemplate($config),
            'system' => SystemListView::getTemplate($config),
            'sublnk' => SublnkListView::getTemplate($config),
            default => ListView::getTemplate($config),
        };
    }

    protected function getAddViewTemplate(string $layout, array $config): string
    {
        return match ($layout) {
            'manual' => AddView::getTemplate($config),
            'standr' => StandrAddView::getTemplate($config),
            'master' => MasterAddView::getTemplate($config),
            'transc' => TranscAddView::getTemplate($config),
            'system' => SystemAddView::getTemplate($config),
            'sublnk' => SublnkAddView::getTemplate($config),
            default => AddView::getTemplate($config),
        };
    }

    protected function getEditViewTemplate(string $layout, array $config): string
    {
        return match ($layout) {
            'manual' => EditView::getTemplate($config),
            'standr' => StandrEditView::getTemplate($config),
            'master' => MasterEditView::getTemplate($config),
            'transc' => TranscEditView::getTemplate($config),
            'system' => SystemEditView::getTemplate($config),
            'sublnk' => SublnkEditView::getTemplate($config),
            default => EditView::getTemplate($config),
        };
    }

    protected function getShowViewTemplate(string $layout, array $config): string
    {
        return match ($layout) {
            'manual' => ShowView::getTemplate($config),
            'standr' => StandrShowView::getTemplate($config),
            'master' => MasterShowView::getTemplate($config),
            'transc' => TranscShowView::getTemplate($config),
            'system' => SystemShowView::getTemplate($config),
            'sublnk' => SublnkShowView::getTemplate($config),
            default => ShowView::getTemplate($config),
        };
    }
}

<?php

namespace MSJFramework\LaravelGenerator\Services\Templates;

use MSJFramework\LaravelGenerator\Services\Templates\Helpers\ErrorHelperTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\Helpers\FormatHelperTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\Helpers\FunctionHelperTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\Helpers\TableExporterTemplate;

class MSJBaseControllerTemplate
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use App\Helpers\Koperasi\ErrorHelper;
use App\Helpers\Koperasi\Export\TableExporter;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MSJBaseController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $syslog;

    protected $format;

    public function __construct()
    {
        $this->syslog = new Function_Helper;
        $this->format = new Format_Helper;
    }

    protected function initializeTableLayout(array $data): array
    {
        $conditions = ['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu']];

        return [
            'table_header' => DB::table('sys_table')->where($conditions + ['list' => '1'])->orderBy('urut')->get(),
            'table_primary' => DB::table('sys_table')->where($conditions + ['primary' => '1'])->orderBy('urut')->get(),
            'table_header_show' => DB::table('sys_table')->where($conditions + ['show' => '1'])->orderBy('urut')->get(),
            'table_header_l' => DB::table('sys_table')->where($conditions + ['position' => '3', 'show' => '1'])->orderBy('urut')->get(),
            'table_header_r' => DB::table('sys_table')->where($conditions + ['position' => '4', 'show' => '1'])->orderBy('urut')->get(),
        ];
    }

    public function auth($data, ?string $action = null, $query = null): mixed
    {
        if ($action) {
            $auth = $data['authorize'] ?? null;
            if (! $auth) {
                return false;
            }

            return match ($action) {
                'add', 'create', 'store' => $auth->add == '1',
                'edit', 'update' => $auth->edit == '1',
                'delete', 'destroy' => $auth->delete == '1',
                'view', 'show', 'index' => $auth->value == '1',
                'approval' => ($auth->approval ?? '0') == '1',
                default => false
            };
        }

        if ($query) {
            $menuWhere = DB::table('sys_dmenu')->where('dmenu', $data['dmenu'])->value('where') ?? '';

            if ($menuWhere && $data['authorize']->rules == '1') {
                $roles = array_map('trim', explode(',', $data['user_login']->idroles));
                $rulesFilter = implode(' OR ', array_map(fn ($role) => "FIND_IN_SET('$role', REPLACE(rules, ' ', ''))", $roles));
                $query->whereRaw("$menuWhere AND ($rulesFilter)");
            } elseif ($menuWhere) {
                $query->whereRaw($menuWhere);
            }

            return $query;
        }

        return false;
    }

    public function search($query, array $fields, ?string $searchTerm = null): mixed
    {
        $search = $searchTerm ?: request('search');

        if ($search) {
            $query->where(function ($q) use ($fields, $search) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }

    public function error(array $data, string $message, string $action = ''): \Illuminate\View\View
    {
        $this->log('E', $data['dmenu'] ?? '', $message.($action ? " - $action" : ''));

        return view('pages.errorpages', array_merge($data, [
            'url_menu' => 'error',
            'title_group' => 'Error',
            'title_menu' => 'Error',
            'errorpages' => $message,
        ]));
    }

    public function transaction(callable $callback, array $data, string $successMsg): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();

            $this->log('C', $data['dmenu'] ?? '', $successMsg);

            return redirect($data['url_menu'] ?? '/')
                ->with('message', $successMsg)
                ->with('class', 'success');
        } catch (\Exception $e) {
            DB::rollback();
            $this->log('E', $data['dmenu'] ?? '', 'Error: '.$e->getMessage());

            return redirect()->back()
                ->with('message', ErrorHelper::format($e))
                ->with('class', 'danger')
                ->withInput();
        }
    }

    public function paginate($collection, int $perPage = 10): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $sliced = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $sliced,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function log(string $type, string $dmenu, string $message): void
    {
        $this->syslog->log_insert($type, $dmenu, $message, $type === 'E' ? '0' : '1');
    }

    public function flash(string $type, string $message): void
    {
        Session::flash('message', $message);
        Session::flash('class', $type === 'success' ? 'success' : 'danger');
    }

    public function redirect(?string $route = null, ?string $message = null, string $type = 'success'): \Illuminate\Http\RedirectResponse
    {
        $redirect = $route ? redirect('/'.$route) : redirect()->back()->withInput();

        if ($message) {
            $redirect->with('message', $message)->with('class', $type === 'success' ? 'success' : 'danger');
        }

        return $redirect;
    }

    public function decrypt(string $encrypted): array
    {
        try {
            return ['success' => true, 'id' => decrypt($encrypted)];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'ID tidak valid!'];
        }
    }

    public function initialize(array $data): array
    {
        return array_merge($data, ['format' => $this->format]);
    }

    public function getUsername(): string
    {
        return session('username', 'system');
    }

    protected function formatPeriode(string $periode): string
    {
        if (strlen($periode) < 6) {
            return $periode;
        }

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $bulan = substr($periode, 0, 2);
        $tahun = substr($periode, 2, 4);

        return ($months[$bulan] ?? $bulan).' '.$tahun;
    }

    protected function getCurrentUserNik(array $data): array
    {
        $currentUserId = $data['user_login']->id ?? null;

        if (! $currentUserId) {
            return ['success' => false, 'error' => 'User ID tidak ditemukan!'];
        }

        $anggota = DB::table('mst_anggota')
            ->where('user_id', $currentUserId)
            ->where('isactive', '1')
            ->first();

        if (! $anggota) {
            return ['success' => false, 'error' => 'Data anggota tidak ditemukan! Silakan hubungi administrator.'];
        }

        return ['success' => true, 'nik' => $anggota->nik, 'anggota' => $anggota];
    }

    public function exportData(string $module, string $type): mixed
    {
        $user = User::find(session('username'));

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        $users_rules = array_map('trim', explode(',', $user->idroles));

        $menu = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->join('sys_dmenu', 'sys_gmenu.gmenu', '=', 'sys_dmenu.gmenu')
            ->where('sys_dmenu.url', $module)
            ->select('sys_gmenu.gmenu as gmenu', 'sys_gmenu.name as gname', 'sys_dmenu.name as dname', 'sys_dmenu.layout as layout', 'sys_dmenu.url as url', 'sys_dmenu.dmenu as dmenu', 'sys_dmenu.tabel as tabel')
            ->first();

        if (! $menu) {
            abort(404, 'Module not found');
        }

        $auth = DB::table('sys_auth')
            ->where(['gmenu' => $menu->gmenu, 'dmenu' => $menu->dmenu])
            ->whereIn('idroles', $users_rules)
            ->first();

        if (! $auth) {
            abort(403, 'Not authorized');
        }

        $data = [
            'user_login' => $user,
            'gmenuid' => $menu->gmenu,
            'dmenu' => $menu->dmenu,
            'title_menu' => $menu->dname,
            'title_group' => $menu->gname,
            'url_menu' => $module,
            'authorize' => $auth,
        ];

        $data = $this->initialize($data);
        $data = array_merge($data, $this->initializeTableLayout($data));

        $controllerName = 'App\\Http\\Controllers\\'.ucfirst($menu->layout == 'manual' ? $module : $menu->layout).'Controller';

        if (! class_exists($controllerName)) {
            abort(500, 'Controller not found');
        }

        $controller = app($controllerName);

        if (! method_exists($controller, 'getExportData')) {
            abort(500, 'Method getExportData tidak ditemukan di '.$controllerName);
        }

        $data = $controller->getExportData($data);

        $exporter = new TableExporter;

        if ($module === 'reportrekap') {
            $referer = request()->header('referer', '');
            $reportType = null;

            if (preg_match('/\/reportrekap\/show\/(\w+)/', $referer, $matches)) {
                $reportType = $matches[1];
            } else {
                $sessionKeys = array_keys(session()->all());
                foreach ($sessionKeys as $key) {
                    if (str_starts_with($key, 'generated_report_')) {
                        $reportType = str_replace('generated_report_', '', $key);
                        break;
                    }
                }
            }

            if (! $reportType) {
                abort(400, 'Report type tidak dapat ditentukan. Pastikan Anda mengakses export dari halaman laporan yang valid.');
            }

            $tableHeaders = DB::table('sys_table')
                ->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1'])
                ->where(function ($query) use ($reportType) {
                    $query->where('note', 'LIKE', "%{$reportType}%")
                        ->orWhere('note', '');
                })
                ->orderBy('urut')
                ->get();

        } else {
            $tableHeaders = $data['table_header'];
        }

        $tableDetails = $data['table_detail'] ?? collect();

        if (method_exists($tableDetails, 'items')) {
            $tableDetails = collect($tableDetails->items());
        } elseif (is_array($tableDetails)) {
            $tableDetails = collect($tableDetails);
        }

        $title = $data['title_menu'] ?? 'Export Data';

        if ($module === 'reportrekap') {
            $reportNames = [
                'pinjaman' => 'Laporan Pinjaman',
                'shu' => 'Laporan SHU',
                'potongan' => 'Laporan Potongan',
                'debitkredit' => 'Laporan Debit & Kredit',
            ];

            if (isset($reportNames[$reportType])) {
                $title = $reportNames[$reportType];
            }
        }

        $exportTotals = $data['export_totals'] ?? [];

        return match ($type) {
            'excel' => $exporter->exportToExcel($tableHeaders->toArray(), $tableDetails, $title, $exportTotals),
            'pdf' => $exporter->exportToPdf($tableHeaders->toArray(), $tableDetails, $title, $exportTotals),
            'print' => view('components.export.print', [
                'tableHeaders' => $tableHeaders,
                'tableDetails' => $tableDetails,
                'title' => $title,
                'html' => $exporter->generatePrintView($tableHeaders->toArray(), $tableDetails, $title, $exportTotals),
            ]),
            default => abort(404, 'Tipe export tidak didukung: '.$type)
        };
    }

    protected function generateFirst($query = null, string $feature = '', ?string $periodeField = null, ?callable $periodeFormatter = null, ?string $periode = null, ?string $successMessage = null)
    {
        if ($periode && $successMessage) {
            Session::put("show_{$feature}_data", true);
            Session::put("current_{$feature}_periode", $periode);

            return redirect()->back()
                ->with('message', $successMessage)
                ->with('class', 'success');
        }

        $showData = Session::has("show_{$feature}_data");
        $data = ['show_data' => $showData];

        if ($showData) {
            $currentPeriode = Session::get("current_{$feature}_periode");

            if ($currentPeriode && $query && $periodeField) {
                $query->where($periodeField, $currentPeriode);
                $data['current_periode'] = $currentPeriode;
                $data['formatted_periode'] = $periodeFormatter ? $periodeFormatter($currentPeriode) : $currentPeriode;
            } else {
                if ($query) {
                    $query->whereRaw('1 = 0');
                }
                $data['current_periode'] = null;
                $data['formatted_periode'] = null;
            }

            Session::forget(["show_{$feature}_data", "current_{$feature}_periode"]);
        } else {
            if ($query) {
                $query->whereRaw('1 = 0');
            }
            $data['current_periode'] = null;
            $data['formatted_periode'] = null;
        }

        return $data;
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        // Create required helpers first
        FormatHelperTemplate::createIfNotExists();
        FunctionHelperTemplate::createIfNotExists();
        ErrorHelperTemplate::createIfNotExists();
        TableExporterTemplate::createIfNotExists();

        $controllerPath = app_path('Http/Controllers/MSJBaseController.php');

        if (! file_exists($controllerPath)) {
            // Create Controllers directory if not exists
            $controllerDir = dirname($controllerPath);
            if (! is_dir($controllerDir)) {
                mkdir($controllerDir, 0755, true);
            }

            file_put_contents($controllerPath, self::getTemplate());
        }
    }
}

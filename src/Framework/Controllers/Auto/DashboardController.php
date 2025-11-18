<?php

namespace App\Http\Controllers;

use App\Helpers\Koperasi\Dashboard\FormHelper;
use App\Helpers\Koperasi\Dashboard\StatsHelper;
use App\Helpers\Koperasi\Dashboard\ChartHelper;
use App\Helpers\Koperasi\Dashboard\DashboardRepositories;

class DashboardController extends Controller
{
    public function index($data)
    {
        // Prepare base data and validate session
        $data = FormHelper::prepareBaseData($data);
        
        if (isset($data['error']) && $data['error']) {
            return redirect($data['redirect'])->withErrors(['error' => $data['message']]);
        }

        $role = $data['role'];
        
        // Get role-specific dashboard data
        switch ($role) {
            case 'anggot':
                $anggota = FormHelper::getMemberByUser($data['user_login']);
                if (!$anggota) {
                    $data['error'] = 'Data anggota tidak ditemukan';
                    $data = FormHelper::ensureHistoriPinjaman($data);
                    return view($data['url'], $data);
                }
                
                $data['anggota'] = $anggota;
                $stats = StatsHelper::getStatsForAnggota($anggota->nik);
                $data = array_merge($data, $stats);
                
                // Get and process SHU data
                $shuRaw = DashboardRepositories::getShuDataByNik($anggota->nik);
                $data['shuData'] = StatsHelper::processShuData($shuRaw);
                break;
                
            case 'kadmin':
                $stats = StatsHelper::getStatsForKadmin();
                $data = array_merge($data, $stats);
                $data['chartData'] = StatsHelper::getChartData();
                $dateInfo = FormHelper::prepareDateInfo();
                $data = array_merge($data, $dateInfo);
                break;
                
            case 'akredt':
                $stats = StatsHelper::getStatsForAkredt();
                $data = array_merge($data, $stats);
                $dateInfo = FormHelper::prepareDateInfo();
                $data = array_merge($data, $dateInfo);
                
                // Get and process SHU data
                $shuRaw = DashboardRepositories::getAllShuData();
                $data['shuData'] = StatsHelper::processShuData($shuRaw);
                break;
                
            case 'ketuum':
                $stats = StatsHelper::getStatsForKetuum();
                $data = array_merge($data, $stats);
                
                // Get and process SHU data
                $shuRaw = DashboardRepositories::getAllShuData();
                $data['shuData'] = StatsHelper::processShuData($shuRaw);
                break;
                
            case 'atrans':
                $stats = StatsHelper::getStatsForAtrans();
                $data = array_merge($data, $stats);
                
                // Get and process SHU data
                $shuRaw = DashboardRepositories::getAllShuData();
                $data['shuData'] = StatsHelper::processShuData($shuRaw);
                break;
        }

        // Ensure historiPinjaman is always set
        $data = FormHelper::ensureHistoriPinjaman($data);

        return view($data['url'], $data);
    }

    public function getChartData()
    {
        $dataType = request('dataType');
        $month = request('month');
        $year = request('year', date('Y'));

        switch ($dataType) {
            case 'pinjaman':
                return response()->json(ChartHelper::getPinjamanStatusChart($month, $year));
            
            case 'anggota':
                return response()->json(ChartHelper::getAnggotaChart());
            
            case 'debitkredit':
                return response()->json(ChartHelper::getDebitKreditChart($month, $year));
            
            case 'departemen':
                return response()->json(ChartHelper::getDepartemenChart($month, $year));
            
            case 'shu':
                return response()->json(ChartHelper::getShuChart($year));
            
            default:
                return response()->json(['error' => 'Invalid data type'], 400);
        }
    }
}
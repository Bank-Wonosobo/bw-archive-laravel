<?php

namespace App\Http\Controllers;

use App\Exceptions\KodeSLIKNotSetException;
use App\Helper\AuthUser;
use App\Http\Requests\PermohonanSlik\StorePermohohonanSlikReq;
use App\Models\KodeSlik;
use App\Models\PermohonanSlik;
use App\Services\PermohonanSlikService;
use App\Traits\NumberToRoman;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanSlikController extends Controller
{
    use NumberToRoman;

    private PermohonanSlikService $permohonanSlikService;

    public function __construct(PermohonanSlikService $permohonanSlikService) {
        $this->permohonanSlikService = $permohonanSlikService;
    }

    public function index() {
        $permohonan_slik = PermohonanSlik::all();
        return view('admin.permohonan_slik.index', compact('permohonan_slik'));
    }

    public function create() {
        $month = $this->numberToRoman(Carbon::now()->month);
        $year = Carbon::now()->year;
        $kode_slik = KodeSlik::where('user_id', AuthUser::user()->id)->first();
        return view('admin.permohonan_slik.create', compact('year', 'month', 'kode_slik'));
    }

    public function store(StorePermohohonanSlikReq $request) {
        $user = AuthUser::user();

        try {
            $result = $this->permohonanSlikService->create($request, $user->id, $user->name);
            if ($request->file('berkas') != null) $this->permohonanSlikService->addBerkas($result->id, $request->file('berkas'));
            return redirect()->route('admin.slik.create', ['permohonan_slik_id' => $result->id])->with('success', 'Berhasil malakukan permohonan, silahkan input data nasabah slik');
        } catch (KodeSLIKNotSetException $e) {
            return redirect()->back()->with('error', 'Kode SLIK belum di set');
        }catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Gagal melakukan permohonan , sedang terjadi maintenance, coba beberapa saat lagi ');
        }
    }

    public function detail($id) {
        $permohonan_slik = PermohonanSlik::find($id);
        return view('admin.permohonan_slik.detail', compact('permohonan_slik'));
    }

    public function history() {
        $user = AuthUser::user();
        $permohonan_slik = PermohonanSlik::where('pemohon', $user->name)->orderBy('created_at', 'DESC')->get();
        return view('admin.permohonan_slik.history', compact('permohonan_slik'));
    }
}
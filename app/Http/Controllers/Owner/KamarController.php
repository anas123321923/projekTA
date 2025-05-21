<?php

namespace App\Http\Controllers\Owner;
use ErrorException;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Owner\KamarService;
use App\Http\Requests\KamarRequest;
use App\Models\{Province,regency,District};
use Illuminate\Support\Facades\DB;


class KamarController extends Controller
{
    protected $kamar;

    public function __construct(KamarService $kamar)
    {
      $this->kamar = $kamar;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      try {
        $result = $this->kamar->index();
        return $result;
      } catch (ErrorException $e) {
        throw new ErrorException($e->getMessage());
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      try {
        $result = $this->kamar->create();
        return $result;
      } catch (ErrorException $e) {
        throw new ErrorException($e->getMessage());
      }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(KamarRequest $request)
    {
      try {
        $result = $this->kamar->store($request);
        return $result;
      } catch (ErrorException $e) {
        throw new ErrorException($e->getMessage());
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
      try {
        $result = $this->kamar->show($slug);
        return $result;
      } catch (ErrorException $e) {
        throw new ErrorException($e->getMessage());
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
      try {
        $result = $this->kamar->edit($slug);
        return $result;
      } catch (ErrorException $e) {
        throw new ErrorException($e->getMessage());
      }
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
    
            // Hapus data terkait di tabel transactions
            DB::table('transactions')->where('kamar_id', $id)->delete();
    
            // Hapus data terkait di tabel alamats (jika ada)
            DB::table('alamats')->where('kamar_id', $id)->delete();
    
            // Hapus data kamar
            DB::table('kamars')->where('id', $id)->delete();
    
            DB::commit();
    
            return redirect()->route('kamar.index')->with('success', 'Data kamar berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('kamar.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
      try {
        $result = $this->kamar->update($id,$request);
        return $result;
      } catch (ErrorException $e) {
        throw new ErrorException($e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  

    // Select Regency
    public function selectRegency(Request $request)
    {
      $regency = regency::where('province_id',$request->province_id)->get();
      return \response()->json($regency);
    }

     // Select District
    public function selectDistrict(Request $request)
    {
      $district = District::where('regency_id',$request->regency_id)->get();
      return \response()->json($district);
    }

    // Del Fasilitas Kamar
    public function delFasilitasKamarService($id)
    {
      return $this->kamar->delFasilitasKamar($id);
    }

    // Del Fasilitas Kamar Mandi
    public function delFasilitasKamarMandiService($id)
    {
      return $this->kamar->delFasilitasKamarMandi($id);
    }

    // Del Fasilitas Bersama
    public function delFasilitasBersamaService($id)
    {
      return $this->kamar->delFasilitasBersama($id);
    }

    // Del Fasilitas Parkir
    public function delFasilitasParkirService($id)
    {
      return $this->kamar->delFasilitasParkir($id);
    }

    // Del Area
    public function delAreaService($id)
    {
      return $this->kamar->delArea($id);
    }

     // Del Foto Kamar
    public function delFotoKamarService($image)
    {
      return $this->kamar->delFotoKamar($image);
    }

    
}



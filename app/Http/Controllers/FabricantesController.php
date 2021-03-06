<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Fabricante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FabricantesController extends Controller {

public function __construct(){

	$this->middleware('auth.basic.once', ['only' => ['store', 'update', 'destroy']]);

}
	

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$fabricantes = Cache::remember('fabricantes', 10/60, function(){

			return Fabricante::simplePaginate(15);
		});

		return response()->json(['siguiente' => $fabricantes->nextPageUrl(), 'anterior'=>$fabricantes->previousPageUrl(), 'datos' => $fabricantes->items()], 200);
		
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		if(!$request->input('nombre') || !$request->input('telefono'))
		{			
			return response()->json(['mensaje' => 'No se completo el proceso', 'codigo' => 422], 422);
		}

		Fabricante::create($request->all());

		return response()->json(['mensaje' => 'Fabricante insertado'], 201);
    }
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$Fabricante = Fabricante::find($id);

		if(!$Fabricante){
			return response()->json(['mensaje' => 'No se encuentra este fabricante', 'codigo' => 404], 404);
		}
		return response()->json(['datos' => $Fabricante], 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$metodo = $request->method();
        $fabricante = Fabricante::find($id);

		if(!$fabricante)
		{
			return response()->json(['mensaje' => 'No se encuentra este fabricante', 'codigo' => 404], 404);
		}

          
		if($metodo === 'PATCH')
		{
			$nombre = $request->input('nombre');
			if($nombre != null && $nombre != ''){
				 $fabricante->nombre = $nombre;
			}

			$telefono = $request->input('telefono');
			if($telefono != null && $telefono != ''){
				 $fabricante->telefono = $telefono;
			}
						
			$fabricante->save();
			return response()->json(['mensaje' => 'Fabricante editado'], 200);		
		}

		$nombre = $request->input('nombre');
		$telefono = $request->input('telefono');
		if(!$nombre || !$telefono){
			return response()->json(['mensaje' => 'Error en los datos', 'codigo' => 404], 404);
		}

		 $fabricante->nombre = $nombre;
		 $fabricante->telefono = $telefono;
		 $fabricante->save();
		return response()->json(['mensaje' => 'Fabricante editado'], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$fabricante = Fabricante::find($id);

		if(!$fabricante){

			return response()->json(['mensaje' => 'No se encuentra este fabricante', 'codigo' => 404], 404);
		}

		$vehiculos = $fabricante->vehiculos;
		if(sizeof($vehiculos) > 0)
		{
			return response()->json(['mensaje' => 'Este fabricante posee vehiculos asociados', 'codigo' => 409], 409);
		}
			$fabricante->delete();
			return response()->json(['mensaje' => 'Fabricante eliminado'], 200);
	}

}

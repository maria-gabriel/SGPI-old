<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Custom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Tarea;
use App\Models\User;
use App\Models\Subtarea;
use App\Models\Proyecto;
use App\Models\cat_docs;
use App\Models\Documento;

class SubtareaController extends Controller
{
    protected $bg;
    public function __construct()
    {
        $this->bg = Custom::where('id_user', 3)->get()->last();
    }
    public function crud(Request $request)
    {
        $respuesta = [];
        $err = "Hubo un problema. Consulte un administrador.";
        try {
            if ($request->has('index')) {
                if ($request->index == "load") {
                    $subtarea = Subtarea::where('id_user', Auth::user()->id)->get(['id AS DT_RowId', 'subtareas.*']);
                    foreach ($subtarea as $sub) {
                        $sub->responsable = $sub->usuarios->nombreCompleto;
                    }
                    $respuesta['data'] = $subtarea;
                    return response()->json($respuesta);
                } elseif ($request->index == "get") {
                    $subtarea = Subtarea::where('id_user', Auth::user()->id)->where('id_tarea', $request->id_tarea)->get(['id AS DT_RowId', 'subtareas.*']);
                    $respuesta['data'] = $subtarea;
                    return response()->json($respuesta);
                }elseif ($request->index == "save") {
                    if ($request->nombre == '') {
                        $err = "Ingrese el nombre.";
                    } elseif ($request->descripcion == '') {
                        $err = "Ingrese la descripción.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->inicio) != 1) {
                        $err = "Fecha inicio inválida.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->final) != 1) {
                        $err = "Fecha final inválida.";
                    }
                    $subtarea = new Subtarea();
                    $subtarea->nombre = $request->nombre;
                    $subtarea->descripcion = $request->descripcion;
                    $subtarea->inicio = $request->inicio;
                    $subtarea->id_tarea = $request->id_tarea;
                    $subtarea->responsable = Auth::user()->id;
                    $subtarea->final = $request->final;
                    $subtarea->id_user = Auth::user()->id;
                    $subtarea->estado = "En curso";
                    $subtarea->save();
                    $nuevo = Subtarea::orderBy('created_at', 'desc')->first();
                    $respuesta['data'][0] = $nuevo;
                    return response()->json($respuesta);
                } elseif ($request->index == "remove") {
                    $subtarea = Subtarea::where('id', $request->id)->delete();
                    $data = Subtarea::all();
                    return response()->json($data);
                } elseif ($request->index == "update") {
                    if ($request->nombre == '') {
                        $err = "Ingrese el nombre.";
                    } elseif ($request->descripcion == '') {
                        $err = "Ingrese la descripción.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->inicio) != 1) {
                        $err = "Fecha inicio inválida.";
                    } elseif (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->final) != 1) {
                        $err = "Fecha final inválida.";
                    }
                    $subtarea = Subtarea::where('id', $request->id)->get()->last();
                    $subtarea->id = $request->id;
                    $subtarea->nombre = $request->nombre;
                    $subtarea->descripcion = $request->descripcion;
                    $subtarea->inicio = $request->inicio;
                    $subtarea->final = $request->final;
                    if($request->has('estado')){
                        if ($subtarea->estado != '') {
                            $subtarea->estado = $request->estado;
                        }
                    }else{
                        $subtarea->estado = 'En curso';
                    }
                    $subtarea->update();
                    $respuesta['data'][0] = $subtarea;
                    return response()->json($respuesta);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $err, 'code' => $e], 404);
        }
    }
    //
    public function index(){
       $bg = $this->bg;
       $subtareas = Subtarea::where('id_user', Auth::user()->id)->get();
       return view('subtareas.index',compact('bg','subtareas'));     
    }

    public function create(Tarea $tarea){
       $bg = $this->bg;
       return view('subtareas.create',compact('bg','tarea'));     
    }

    public function create2(Subtarea $subtarea)
    {
        $bg = $this->bg;
        $tarea = Tarea::where('id', $subtarea->id_tarea)->get()->last();
        $usuarios = User::get()->where('area',$tarea->area)->pluck('nombreCompleto', 'id'); 
        return view('subtareas.responsable', compact('bg', 'subtarea','usuarios'));
    }

    public function documentos(Subtarea $subtarea){
        $bg = $this->bg;
        $cat_doc = cat_docs::where('iactivo',1)->pluck('nombre', 'id'); 
        $documentos = Documento::where('id_user', Auth::user()->id)->where('id_subtarea', $subtarea->id)->orderBy('created_at', 'DESC')->get();
        foreach ($documentos as $documento) {
            $xx = strtolower($documento->extension);
            if($xx=='xls' || $xx=='csv' || $xx=='xlsx'){
                $documento->tipo = 'fa-file-excel-o text-success';
            }elseif($xx=='pdf') {
                $documento->tipo = 'fa-file-pdf-o text-danger';
            }elseif($xx=='doc' || $xx=='docx') {
                $documento->tipo = 'fa-file-word-o text-info';
            }elseif($xx=='ppt' || $xx=='pptx') {
                $documento->tipo = 'fa-file-powerpoint-o text-secondary';
            }elseif($xx=='zip' || $xx=='rar' || $xx=='bin') {
                $documento->tipo = 'fa-file-zip-o text-primary';
            }elseif($xx=='txt') {
                $documento->tipo = 'fa-file-text-o text-warning';
            }elseif($xx=='png' || $xx == 'jpg' || $xx == 'jpeg' || $xx == 'gif' || $xx == 'raw' || $xx == 'svg' || $xx == 'psd') {
                $documento->tipo = 'fa-file-image-o text-dribbble';
            }elseif($xx=='js' || $xx == 'php' || $xx == 'html' || $xx == 'css' || $xx == 'java' || $xx == 'sql') {
                $documento->tipo = 'fa-file-code-o text-instagram';
            }elseif($xx=='mp3' || $xx == 'mp4' || $xx == 'mov' || $xx == 'avi' || $xx == 'wav') {
                $documento->tipo = 'fa-file-audio-o text-light';
            }else{
                $documento->tipo = 'fa-file-o text-dark';
            }
        }
        return view('subtareas.documentos',compact('bg','documentos','subtarea','cat_doc'));     
     }

     public function store(Request $request, Subtarea $subtarea){
        try{
        $archi=new Documento();
        $archi->nombre=$request->nombre;
        $nombreout = str_replace(' ', '-', $request->nombre);
        $archi->descripcion=$request->descripcion;
        $archi->url='/SGPI/documents/subtareas';
        $archi->url_edit='documents/subtareas';
        $archi->id_user=Auth::user()->id;
        $usuario = User::where('id', Auth::user()->id)->get()->last();
        $archi->id_area=$usuario->area;
        $archi->cat_doc=$request->tipo;
        $mytime = Carbon::now()->format('d-m-Y_H-i');
        $extension = $request->archivo->getClientOriginalExtension();
        $nombre = Auth::user()->id.'_'.$nombreout.'_'.$mytime.'.'.$extension;
        $archi->nombre_doc=$nombre;
        $archi->tipo='Subtarea';
        $archi->extension=$extension;
        $archi->id_tarea=$subtarea->id;
        $archi->save();
        $request->archivo->move('documents/subtareas', $nombre);

            return back()->with('ok', 'ok');
        }catch(\Exception $e){
            return back()->with('nook', $e->getMessage());
        }
    }

    public function store2(Request $request, Subtarea $subtarea){
        try{
        $subtarea->responsable = $request->responsable;
        $subtarea->update();

            return back()->with('ok', 'ok');
        }catch(\Exception $e){
            return back()->with('nook', $e->getMessage());
        }
    }

    /*public function save(Request $request){
      $respuesta = [];
      if($request->has('index')){
        $tar = Subtarea::where('id_user', Auth::user()->id)->where('id_tarea', $request->id_tarea)->get(['id AS DT_RowId', 'subtareas.*']);
          $respuesta['data'] = $tar;
          return response()->json($respuesta);
      }else{
          $tar = new Subtarea();
          $tar->nombre = $request->nombre;
          $tar->descripcion = $request->descripcion;
          $tar->inicio = $request->inicio;
          $tar->final = $request->final;
          $tar->responsable = Auth::user()->id;
          $tar->id_tarea = $request->id_tarea;
          $tar->id_user = Auth::user()->id;
          $tar->estado = "En curso";
          $tar->save();
          $nuevo = Subtarea::orderBy('created_at', 'desc')->where('id_user', Auth::user()->id)->where('id_tarea', $request->id_tarea)->first();
          $respuesta['data'][0] = $nuevo;
          return response()->json($respuesta);
         }
      
     }

     public function remove(Request $request){
         $tar=Subtarea::where('id',$request->id)->delete();
         $data = Subtarea::all();
         return response()->json($data);
     }

     public function update(Request $request){
         $respuesta = [];
         $tar = Subtarea::where('id', $request->id)->get()->last();
         $tar->id = $request->id;
         $tar->nombre = $request->nombre;
         $tar->descripcion = $request->descripcion;
         $tar->inicio = $request->inicio;
         $tar->final = $request->final;
         $tar->update();
         $respuesta['data'][0] = $tar;
         return response()->json($respuesta);
     }*/
}

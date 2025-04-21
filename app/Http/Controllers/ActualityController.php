<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualityFormRequest;
use App\Http\Requests\CoverUpdateRequest;
use App\Http\Resources\ActualityCollection;
use App\Models\Actuality;
use App\Models\Category;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActualityController extends Controller
{

    public function index()
    {
        return view('actualities.index', ['actualities' => Actuality::paginate(25)]) ;
    }


    public function create()
    {
        $actuality = new Actuality();
        $categories = Category::all();
        return view('actualities.form', ['actuality' => $actuality, 'categories' => $categories]);
    }

    public function edit(Actuality $actuality)
    {
        $categories = Category::all();

        return view('actualities.form', ['actuality' => $actuality, 'categories' => $categories]);
    }

    public function show(Actuality $actuality)
    {
        return $this->success(new ActualityCollection($actuality));
    }
    public function store(ActualityFormRequest $request)
    {
        $validatedData = $request->validated();
        
            // Traitement de la couverture principale
            $validatedData['cover_path'] = $this->upload_file($request->file('cover'), 'cover', 'actualities/covers');
            unset($validatedData['cover']);

            // Création de l'actualité
            $actuality = Actuality::create($validatedData);
        
            // Traitement des images supplémentaires
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    // Création de la galerie associée à l'actualité nouvellement créée
                    $actuality->galleries()->create(['path' => $this->upload_file($image, 'additional_images', 'actualities/additional_images'),]);
                }
            }
        
            toastr()->success(" L'actualité a bien été créée ! ", 'Congrats', ['timeOut' => 8000]);
            return redirect()->route('actuality.index');
    }
    
    public function update(CoverUpdateRequest $request, Actuality $actuality)
    {
        $data = $request->validated();
    
        // Traitement de la couverture principale
        if ($request->hasFile('cover')) {
            Storage::delete("public/actualities/covers/{$actuality->cover_path}");
            $actuality->update(['cover_path' => $this->upload_file($request->file('cover'), 'cover', 'actualities/covers')]);
        }
    
        unset($data['cover']);
    
        // Traitement des images supplémentaires
        if ($request->hasFile('additional_images')) {
            // Récupérer les anciennes images supplémentaires de l'équipe
            $oldImages = $actuality->galleries()->get();
    
            // Supprimer les enregistrements des anciennes images de la base de données
            $actuality->galleries()->delete();
    
            // Parcourir les anciennes images et les supprimer du dossier de stockage
            foreach ($oldImages as $image) {
                Storage::delete("public/actualities/additional_images/{$image->path}");
            }
    
            // Ajouter les nouvelles images supplémentaires à la relation et les stocker dans le dossier de stockage
            foreach ($request->file('additional_images') as $image) {
                $gallery = Gallery::create([
                    'path' => $this->upload_file($image, 'additional_images', 'actualities/additional_images'),
                    'galleriestable_id' => $actuality->id,
                    'galleriestable_type' => Actuality::class,
                ]);
            }
        }
    
        // Mise à jour de l'équipe
        $actuality->update($data);
    
        toastr()->success(" L'actualité a bien été modifiée ! ", 'Félicitations', ['timeOut' => 8000]);
        return redirect()->route('actuality.index');
    }
    
    

    public function destroy(Actuality $actuality)
{
    // Supprimer le cover de l'équipe s'il existe
    if ($actuality->cover_path) {
        Storage::disk('public')->delete("actualities/covers/{$actuality->cover_path}");
    }

    // Supprimer les images supplémentaires de la galerie de l'équipe
    foreach ($actuality->galleries as $gallery) {
        Storage::disk('public')->delete("actualities/additional_images/{$gallery->path}");
    }

    // Supprimer l'équipe et toutes ses relations
    $actuality->delete();

    toastr()->success("L'actualité a été supprimée avec succès !", 'Félicitations', ['timeOut' => 8000]);
    return redirect()->route('actuality.index');
}
    protected function upload_file($file, string $fileLabel, string $path): string
    {
        if ($file) {
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storePubliclyAs('public/' . $path, $fileName);

            return $fileName;
        }
        return 'null';
    }
}

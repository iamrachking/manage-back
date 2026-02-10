<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectFormRequest;
use App\Http\Requests\CoverUpdateRequest;
use App\Http\Requests\ProjectCoverUpdate;
use App\Http\Resources\ProjectCollection;
use App\Models\Project;
use App\Models\Category;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.index', ['projects' => Project::latest()->paginate(25)]);
    }

    public function create()
    {
        $project = new Project();
        return view('projects.form', ['project' => $project]);
    }

    public function store(ProjectFormRequest $request)
    {
        $validatedData = $request->validated();
        
            // Traitement de la couverture principale
            $validatedData['cover_path'] = $this->upload_file($request->file('cover'), 'cover', 'projects/covers');
            unset($validatedData['cover']);

            // Création de l'actualité
            $project = Project::create($validatedData);
        
            // Traitement des images supplémentaires
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    // Création de la galerie associée à l'actualité nouvellement créée
                    $project->galleries()->create(['path' => $this->upload_file($image, 'additional_images', 'projects/additional_images'),]);
                }
            }
        
            toastr()->success(" Le project a bien été créée ! ", 'Congrats', ['timeOut' => 8000]);
            return redirect()->route('project.index');
    }
    
    public function update(ProjectCoverUpdate $request, Project $project)
    {
        $data = $request->validated();
    
        // Traitement de la couverture principale
        if ($request->hasFile('cover')) {
            Storage::delete("public/projects/covers/{$project->cover_path}");
            $project->update(['cover_path' => $this->upload_file($request->file('cover'), 'cover', 'projects/covers')]);
        }
    
        unset($data['cover']);
    
        // Traitement des images supplémentaires
        if ($request->hasFile('additional_images')) {
            // Récupérer les anciennes images supplémentaires de l'équipe
            $oldImages = $project->galleries()->get();
    
            // Supprimer les enregistrements des anciennes images de la base de données
            $project->galleries()->delete();
    
            // Parcourir les anciennes images et les supprimer du dossier de stockage
            foreach ($oldImages as $image) {
                Storage::delete("public/projects/additional_images/{$image->path}");
            }
    
            // Ajouter les nouvelles images supplémentaires à la relation et les stocker dans le dossier de stockage
            foreach ($request->file('additional_images') as $image) {
                $gallery = Gallery::create([
                    'path' => $this->upload_file($image, 'additional_images', 'projects/additional_images'),
                    'galleriestable_id' => $project->id,
                    'galleriestable_type' => Project::class,
                ]);
            }
        }
    
        // Mise à jour de l'équipe
        $project->update($data);
    
        toastr()->success(" Le project a bien été modifiée ! ", 'Félicitations', ['timeOut' => 8000]);
        return redirect()->route('project.index');
    }


    public function edit(Project $project)
    {
        return view('projects.form', ['project' => $project]);
    }

    public function show(Project $project)
    {
        return $this->success(new ProjectCollection($project));
    }
    public function destroy(Project $project)
    {
        // Supprimer le cover de l'équipe s'il existe
        if ($project->cover_path) {
            Storage::disk('public')->delete("projects/covers/{$project->cover_path}");
        }
    
        // Supprimer les images supplémentaires de la galerie de l'équipe
        foreach ($project->galleries as $gallery) {
            Storage::disk('public')->delete("projects/additional_images/{$gallery->path}");
        }
    
        // Supprimer l'équipe et toutes ses relations
        $project->delete() ;
    
        toastr()->success("Le projet a été supprimée avec succès !", 'Félicitations', ['timeOut' => 8000]);
        return redirect()->route('project.index');
    }
}

<x-app-layout>
@section('title', 'Les Projets')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><strong>LES PROJETS</strong></h1>
                        <a href="{{ route('project.create') }}" class="d-flex align-items-center btn btn-primary">
                            Ajouter un projet <i class="fas fa-plus" style="margin-left:5px;"></i>
                        </a>
                    </div>
                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Année</th>
                                <th scope="col">Nom Projet</th>
                                <th scope="col">Ville</th>
                                <th scope="col">Développeur</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                            <tr>
                                <th scope="row">{{ $project->id }}</th>
                                <td>{{ $project->year }}</td>
                                <td>{{ $project->project_name }}</td>
                                <td>{{ $project->city }}</td>
                                <td>{{ $project->developer }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end w-100">
                                        <a href="{{ route('project.edit', $project) }}" class="d-flex align-items-center btn btn-primary">
                                            Editer <i class="far fa-pen-to-square" style="margin-left:5px;"></i>
                                        </a>
                                        <form action="{{ route('project.destroy', $project) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button class="d-flex align-items-center btn btn-danger">
                                                Supprimer <i class="fas fa-trash" style="margin-left:5px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun projet trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
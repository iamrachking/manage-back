<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @section('title', $actuality->exists ? 'Modifier une actualité' : 'Créer une actualité')
    <div class="container mt-5">
        <h1 class="pb-3 fw-bold h3">@yield('title')</h1>
        <form class="vstack gap-2" enctype="multipart/form-data"
            action="{{ route($actuality->exists ? 'actuality.update' : 'actuality.store', $actuality) }}" method="POST">
            @csrf
            
            @method($actuality->exists ? 'PUT' : 'POST')
            <div class="row">
                <div class="col-md-6">
                    <x-forms.input name='title' :value="$actuality->title" label='Titre' />
                </div>
                <div class="col-md-6">
                    <x-forms.select name="category_id" label="Catégorie">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id == $actuality->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-md-6">
                    <x-forms.input 
                        type="date" 
                        name='published_at' 
                        :value="$actuality->published_at ? $actuality->published_at->format('Y-m-d') : ''" 
                        label='Date de publication' 
                    />
                </div>
                <div class="col-md-6">
                    <x-forms.inputfile type="file"  name='cover' :value="$actuality->cover_path" label='Importez une image(Couverture)'   />
                    @if ($actuality->cover_path)
                        <div class="mt-2 w-20 h-20 overflow-hidden rounded-lg border border-gray-300" style="display: flex; justify-content: center;">
                            <img src="{{ asset("/storage/actualities/covers/" . $actuality->cover_path) }}" alt="Image actuelle"
                                class="w-full h-full object-cover" style="width: 100%; height: auto;">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <x-forms.inputfile type="file" name='additional_images[]' label='Importez des images(Supplementaire)' multiple="true" />
                    <div class="row gap-2">
                        @if ($actuality->galleries())
                        @foreach ($actuality->galleries as $gallery)
                            <div class="mt-2 w-20 h-20 overflow-hidden rounded-lg border border-gray-300" style="display: flex; align-items:center; ">
                                <img src="{{ asset("/storage/actualities/additional_images/" . $gallery->path) }}" alt="Image supplémentaire"
                                    class="w-full h-full object-cover" style="width: 100%; height: auto;">
                            </div>
                        @endforeach
                    @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <x-forms.textarea name='description' :value="$actuality->description" label='Descriptions' />
                </div>
            </div>
            <div class="text-center mt-5" style="padding-bottom: 50px;">
                <button class="btn btn-primary w-25">
                    @if ($actuality->exists)
                        Modifier
                    @else
                        Créer
                    @endif
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
@extends('app')

@section('content')
    <form action="{{ route('items.store') }}" class="create" method="post" />
        <section class="module-container">
            <header>
                <div class="section-title">Add application</div>
                <div class="module-actions">
                    <button type="submit"class="button"><i class="fa fa-plus"></i><span>Save</span></button>
                </div>
            </header>
            <div class="create">
                {!! csrf_field() !!}
                <div class="input">
                    <label>Application name</label>
                    <input type="text" name="title" value="{{ old('title') }}" />
                </div>
                <div class="input">
                    <label>Colour</label>
                    <input type="text" name="colour" value="{{ old('colour') }}" />
                </div>
                <div class="input">
                    <label>URL</label>
                    <input type="text" name="url" value="{{ old('url') }}" />
                </div>
            </div>

        </section>

</form>
@endsection
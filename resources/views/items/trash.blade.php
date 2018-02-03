@extends('app')

@section('content')
        <section class="module-container">
            <header>
                <div class="section-title">
                    Showing Deleted Applications
                </div>
                <div class="module-actions">
                    <a href="{{ route('items.index') }}" title="" class="button"><i class="fa fa-ban"></i><span>Cancel</span></a>
                </div>
            </header>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Url</th>
                        <th class="text-center" width="100">Restore</th>
                        <th class="text-center" width="100">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @if($trash->first())
                        @foreach($trash as $app)
                            <tr>
                                <td>{{ $app->title }}</td>
                                <td>{{ $app->description }}</td>
                                <td>{{ $app->url }}</td>
                                <td class="text-center"><a href="{!! route('items.restore', $app->id) !!}" title="Restore {!! $app->title !!}"><i class="fas fa-edit"></i></a></td>
                                <td class="text-center">
                                        {!! Form::open(['method' => 'DELETE','route' => ['items.destroy', $app->id],'style'=>'display:inline']) !!}
                                        <input type="hidden" name="force" value="1" />
                                        <button type="submit"><i class="fa fa-trash-alt"></i></button>
                                        {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="form-error text-center">
                                <strong>No items found</strong>
                            </td>
                        </tr>
                    @endif

                
                </tbody>
            </table>
        </section>


@endsection
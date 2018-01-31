@extends('app')

@section('content')


            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Url</th>
                        <th class="text-center" width="100">Edit</th>
                        <th class="text-center" width="100">Delete</th>
                    </tr>
                </thead>
                <tbody>
                <?php /*
                    @if($apps->first())
                        @foreach($apps as $app)
                            <tr>
                                <td>{{ $app->title }}</td>
                                <td>{{ $app->description }}</td>
                                <td>{{ $app->url }}</td>
                                <td class="text-center"><a href="{!! route('items.edit', $app->id) !!}" title="Edit {!! $app->name !!}"><i class="fa fa-pencil"></i></a></td>
                                <td class="text-center">
                                        <a href="{!! route('items.delete', $app->id) !!}" title="Delete {!! $app->name !!}" class="confirm-delete"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="form-error text-center">
                                <strong>No items found</strong>
                            </td>
                        </tr>
                    @endif

                
                @if($apps->lastPage() > 1)
                    <tr>
                        <td colspan="10" class="text-center">{!! $apps->links() !!}</td>
                    </tr>
                @endif
                */ ?>
                </tbody>
            </table>


@endsection
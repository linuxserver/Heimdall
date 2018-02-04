@extends('app')

@section('content')

    @foreach ($groups as $group)
        <section class="module-container">
            <header>
                <div class="section-title">
                    {{ $group->title }}

                </div>
            </header>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th style="width: 60%;">Value</th>
                        <th class="text-center" style="width: 75px;">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($group->settings) > 0)
                        @foreach ($group->settings as $setting)
                            <tr>
                                <td>{{ $setting->label }}</td>
                                <td>
                                    @php($type = explode('|', $setting->type)[0])
                                    @if ($type == 'image')
                                        @if(!empty($setting->value))
                                        <a href="/uploads/settings/{{ $setting->value }}" title="View" target="_blank">View</a>
                                        @else
                                        - not set -
                                        @endif
                                    @elseif ($type == 'select')
                                    @if ($setting->value == 1)
                                    YES
                                    @else
                                    NO
                                    @endif
                                    @else
                                    {!! $setting->value !!}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if((bool)$setting->system !== true)
                                    <a href="{!! route('settings.edit', ['id' => $setting->id]) !!}" title="Edit {!! $setting->label !!}" class="secondary"><i class="fa fa-pencil"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else

                        <tr>
                            <td colspan="3" class="form-error text-center">
                                <strong>No items found</strong>
                            </td>
                        </tr>
                    @endif

                
                </tbody>
            </table>
        </section>
    @endforeach

@endsection
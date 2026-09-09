                    {{-- Opens the sidenav pin list, which is an admin control,
                         so it is gated with it. --}}
                    @if($enable_auth_admin_controls)
                    <?php $addclass = (isset($ajax)) ? ' active' : ''; ?>
                    <section class="add-item{{ $addclass }}">
                        <a id="add-item" href="">{{ __('app.dash.pin_item') }}</a>
                    </section>
                    @endif

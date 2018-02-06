<h2>Config (optional)</h2>
<div class="items">
    <input type="hidden" name="config[type]" value="\App\SupportedApps\Nzbget" />
    <div class="input">
        <label>Username</label>
        {!! Form::text('config[username]', null, array('placeholder' => 'Username', 'class' => 'form-control')) !!}
    </div>
    <div class="input">
        <label>Password</label>
        {!! Form::text('config[password]', null, array('placeholder' => 'Password', 'class' => 'form-control')) !!}
    </div>
</div>
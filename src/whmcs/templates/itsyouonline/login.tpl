<p class="center">
    {if $logo}
        <a href="{$WEB_ROOT}/"><img src="{$logo}" class="img-respond img-logo" alt="{$companyname} logo"/></a>
    {else}
        <a href="{$WEB_ROOT}/"><img src="{$WEB_ROOT}/templates/{$template}/images/logo.png" class="img-respond img-logo"
                                    alt="{$companyname} logo"/></a>
    {/if}
</p>

<div class="client-login">
    <div class="title">
        {$LANG.clientlogin_title} {$companyname}
    </div>

    <div class="feilds">

        {include file="$template/includes/pageheader.tpl" title=$LANG.login desc="{$LANG.restrictedpage}"}

        {if $incorrect}
        {include file="$template/includes/alert.tpl" type="error" msg=$LANG.loginincorrect textcenter=true}
        {elseif $verificationId && empty($transientDataName)}
            {include file="$template/includes/alert.tpl" type="error" msg=$LANG.verificationKeyExpired textcenter=true}
    {elseif $ssoredirect}
        {include file="$template/includes/alert.tpl" type="info" msg=$LANG.sso.redirectafterlogin textcenter=true}
    {/if}


        {* This part is added *}
    <a class="btn btn-primary" href="{$custom_oauth2_login_url}">{$LANG.loginbutton}</a>
        {* Original login form *}
        {*
            <form method="post" action="{$systemsslurl}dologin.php" role="form">
                <div class="form-group">
                    <label for="inputEmail">{$LANG.clientareaemail}</label>
                    <input type="email" name="username" class="form-control" id="inputEmail" placeholder="{$LANG.enteremail}" autofocus>
                </div>

                <div class="form-group">
                    <label for="inputPassword">{$LANG.clientareapassword}</label>
                    <input type="password" name="password" class="form-control" id="inputPassword" placeholder="{$LANG.clientareapassword}" autocomplete="off">
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="rememberme" /> {$LANG.loginrememberme}
                    </label>
                </div>

                <div class="center">
                    <input id="login" type="submit" class="btn btn-primary" value="{$LANG.loginbutton}" />
                </div>
            </form>
        *}

    </div><!-- .feilds -->

    <div class="help">

        {*This part is modified*}
        <p><a href="{$custom_oauth2_login_url}">{$LANG.clientlogin_register}</a></p>
        {*Original register button and pwreset*}
        {*<p><a href="{$WEB_ROOT}/pwreset.php">{$LANG.loginforgotteninstructions}</a></p>*}
        {*<p><a href="{$WEB_ROOT}/register.php">{$LANG.clientlogin_register}</a></p>*}

    </div><!-- .help -->


</div><!-- .client-login -->
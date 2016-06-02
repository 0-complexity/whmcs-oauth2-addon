<div id="progress" class="waiting">{* Markup for the load progress bar *}
    <dt></dt>
    <dd></dd>
</div>

<div class="toolbar">
    <div class="toolbarinner">

        {if $feature_language eq "on"}
            <div class="leftmenu">
                <ul>
                    <li class="arrowdrop"><a href="javascript:void(0);"
                                             class="flag-{$LANG.language_active} flag-active">{$LANG.language_active}</a>

                        <ul class="children">
                            {include file="$template/includes/languages.tpl"} {* This line loads the language flags and links *}
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- .leftmenu -->
        {else}
            {if $filename eq "cart"}
                <p>{$LANG.toolbar_text_cart}</p>
            {else}
                <p>{$LANG.toolbar_text}</p>
            {/if}
        {/if}

        <div class="rightmenu">
            <ul>
                {if $filename eq "cart"}
                    <li><a href="{$WEB_ROOT}/contact.php" target="_blank"
                           class="contact">{$LANG.toolbar_menu_contact}</a></li>
                {else}
                    {if $loggedin}
                        <li class="welcomeback">{$LANG.toolbar_menu_welcome} {$loggedinuser.firstname}!</li>
                        <li class="arrowdrop"><a href="javascript:void(0);"
                                                 class="myaccount">{$LANG.toolbar_menu_account}</a>

                            <ul class="children">
                                <li class="notifications"><a href="#client-notifications"
                                                             class="open-popup-link{if $clientAlerts|count eq "0"} notifications-color1{else} notifications-color2{/if}">{$clientAlerts|count} {$LANG.notifications}</a>
                                </li>
                                <li><a href="{$WEB_ROOT}/clientarea.php">{$LANG.toolbar_menu_portal}</a></li>
                                <li><a href="{$WEB_ROOT}/clientarea.php?action=details">{$LANG.toolbar_menu_details}</a>
                                </li>
                                <li>
                                    <a href="{$WEB_ROOT}/clientarea.php?action=invoices">{$LANG.toolbar_menu_invoices}</a>
                                </li>
                                {if $condlinks.addfunds}
                                    <li>
                                    <a href="{$WEB_ROOT}/clientarea.php?action=addfunds">{$LANG.toolbar_menu_funds}</a>
                                    </li>{/if}
                                {if $condlinks.masspay}
                                    <li>
                                    <a href="{$WEB_ROOT}/clientarea.php?action=masspay&all=true">{$LANG.masspaytitle}</a>
                                    </li>{/if}
                                {if $condlinks.updatecc}
                                    <li><a href="{$WEB_ROOT}/clientarea.php?action=creditcard">{$LANG.navmanagecc}</a>
                                    </li>{/if}
                                <li>
                                    <a href="{$WEB_ROOT}/clientarea.php?action=products">{$LANG.toolbar_menu_products}</a>
                                </li>
                                <li>
                                    <a href="{$WEB_ROOT}/clientarea.php?action=products">{$LANG.toolbar_menu_services}</a>
                                </li>
                                <li><a href="{$WEB_ROOT}/clientarea.php?action=domains">{$LANG.toolbar_menu_domains}</a>
                                </li>
                                <li><a href="{$WEB_ROOT}/clientarea.php?action=quotes">{$LANG.toolbar_menu_quotes}</a>
                                </li>
                                <li><a href="{$WEB_ROOT}/clientarea.php?action=emails">{$LANG.toolbar_menu_emails}</a>
                                </li>
                                <li><a href="{$WEB_ROOT}/supporttickets.php">{$LANG.toolbar_menu_tickets}</a></li>
                                {if $condlinks.pmaddon}
                                    <li>
                                    <a href="{$WEB_ROOT}/index.php?m=project_management">{$LANG.clientareaprojects}</a>
                                    </li>{/if}
                                {if $feature_affiliates eq "on"}
                                    <li><a href="{$WEB_ROOT}/affiliates.php">{$LANG.toolbar_menu_commissions}</a>
                                    </li>{/if}
                                <li><a href="{$WEB_ROOT}/cart.php?gid=addons">{$LANG.domainaddons}</a></li>
                            </ul>
                        </li>
                        {if $clientAlerts|count eq "0"}{else}
                            <li class="notificationnumber"><a href="#client-notifications"
                                                              class="open-popup-link notifications-color2">{$clientAlerts|count}</a>
                            </li>{/if}
                        <li><a href="{$WEB_ROOT}/logout.php">{$LANG.toolbar_menu_logout}</a></li>
                    {else}
                        <li><a href="{$WEB_ROOT}/contact.php" class="contact">{$LANG.toolbar_menu_contact}</a></li>
                        {* This part is modified *}
                        <li><a href="{$custom_oauth2_login_url}" class="register">{$LANG.loginbutton}</a></li>
                        {*<li><a href="{$WEB_ROOT}/register.php" class="register">{$LANG.toolbar_menu_register}</a></li>*}
                        {*<li><a href="{$WEB_ROOT}/clientarea.php" class="myaccount">{$LANG.toolbar_menu_clientarea}</a>*}
                        </li>
                    {/if}
                {/if}
            </ul>
        </div><!-- .rightmenu -->

    </div><!-- .toolbarinner -->
</div><!-- .toolbar -->
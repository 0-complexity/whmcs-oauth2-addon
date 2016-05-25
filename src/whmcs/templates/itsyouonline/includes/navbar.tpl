{* This if is added *}
{if $loggedin}
    {foreach $navbar as $item}
        <li menuItemName="{$item->getName()}"{if $item->hasChildren()} class="dropdown"{elseif $item->getClass()} class="{$item->getClass()}"{/if}
            id="{$item->getId()}">
            <a {if $item->hasChildren()}class="dropdown-toggle" data-toggle="dropdown" href="#"
               {else}href="{$item->getUri()}"{/if}{if $item->getAttribute('target')} target="{$item->getAttribute('target')}"{/if}>
                {if $item->hasIcon()}<i class="{$item->getIcon()}"></i>&nbsp;{/if}
                {$item->getLabel()}
                {if $item->hasBadge()}&nbsp;<span class="badge">{$item->getBadge()}</span>{/if}
                {if $item->hasChildren()}&nbsp;<b class="caret"></b>{/if}
            </a>
            {if $item->hasChildren()}
                <ul class="dropdown-menu">
                    {foreach $item->getChildren() as $childItem}
                        {* This if is added *}
                        {if strpos($childItem->getUri(), 'changepw') == false}
                            <li menuItemName="{$childItem->getName()}"{if $childItem->getClass()} class="{$childItem->getClass()}"{/if}
                                id="{$childItem->getId()}">
                                <a href="{$childItem->getUri()}"{if $childItem->getAttribute('target')} target="{$childItem->getAttribute('target')}"{/if}>
                                    {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                                    {$childItem->getLabel()}
                                    {if $childItem->hasBadge()}&nbsp;<span
                                            class="badge">{$childItem->getBadge()}</span>{/if}
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}
        </li>
    {/foreach}
{/if}
<div id="easy-opt-out-main-wrapper" class="crm-container">
    <p>{$message}</p>
    {foreach from=$elementNames item=elementName}
    <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
    </div>
    {/foreach}
</div>

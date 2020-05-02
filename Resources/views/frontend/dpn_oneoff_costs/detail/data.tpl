{extends file="parent:frontend/detail/data.tpl"}

{block name='frontend_detail_data_tax' prepend}

    {block name="dpn_oneoff_costs_info"}
        {if $sArticle.oneoff_costs_price}
            <p class="product--tax">{$sArticle.oneoff_costs_label}: {$oneoffCostsPrice|currency}</p>
        {/if}
    {/block}

{/block}
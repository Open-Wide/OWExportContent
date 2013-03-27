{*?template charset=UTF-8*}
<div class="float-break"></div>
<div class="context-block">
    <div class="box-header">
        <div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl">
            <div class="box-tr">
                <h1 class="context-title">{'Migration Arborescence - Import'|i18n('owexportcontent')}</h1><div class="header-subline"></div>
            </div>
        </div></div></div></div>
    </div>
    
    <div class="box-content">    
        <div class="content-navigation-childlist">
            <p>Import des contenus</p>
        </div>
    </div>
    
    <div class="box-ml"><div class="box-mr"><div class="box-content">        
        <div class="content-navigation-childlist">   
{if and(is_set($fileList), $fileList|count())}
	{include uri='design:migrate/listfile.tpl' fileList=$fileList}
{else}
	<p class="error">Aucuns fichiers d'export de contenus trouvés</p>
{/if}            
        </div>
    </div></div></div>
</div>
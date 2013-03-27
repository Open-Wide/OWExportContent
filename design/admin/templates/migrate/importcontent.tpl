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
	{def $compteur = 0}
			<table class="list" cellspacing="0">
				<tr class="bgdark">
					<th>Nom du fichier</th>
					<th>Action</th>
				</tr>
	{foreach $fileList as $file}
				<tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}">
					<td>{$file}</td>
					<td>
					  <a title="import" href={concat('importfile/(file)/', $file)}><img src={'images/import.gif'|ezdesign()} /></a>
					  <a title="delete" href={concat('deletefile/(file)/', $file)}><img src={'images/delete.gif'|ezdesign()} /></a>
					</td>
				</tr>
		{set $compteur = $compteur|inc()} 
	{/foreach}
			</table>
{else}
	<p class="error">Aucuns fichiers d'export de contenus trouvés</p>
{/if}            
        </div>
    </div></div></div>
</div>
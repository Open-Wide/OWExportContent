{*?template charset=UTF-8*}
<div class="float-break"></div>
<div class="context-block">
    <div class="box-header">
        <div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl">
            <div class="box-tr">
                <h1 class="context-title">{'Migration Arborescence'|i18n('owexportcontent')}</h1><div class="header-subline"></div>
            </div>
        </div></div></div></div>
    </div>
    
    <div class="box-content">    
        <div class="content-navigation-childlist">
            <p>Extension qui permet de migrer un arbo complete (content object)</p>
        </div>
    </div>
    
    <div class="box-ml"><div class="box-mr"><div class="box-content">        
        <div class="content-navigation-childlist">   
            Que voulez vous faire : 
            <ul>
                <li><a href={'owexportcontent/exportcontent'|ezurl()}>Exporter toute une arborescense à partir d'un noeud ? </a></li>
                <li><a href={'owexportcontent/exportclass'|ezurl()}>Exporter toute les classes ? </a></li>
                <li><a href={'owexportcontent/importcontent'|ezurl()}>Importer toute une arborescense d'un fichier ?</a></li>
                <li><a href={'owexportcontent/importclass'|ezurl()}>Importer toute les classes d'un fichier ?</a></li>
            </ul>    
            
        </div>
    </div></div></div>
</div>
{/if}
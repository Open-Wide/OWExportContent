{*?template charset=UTF-8*}
<div class="float-break"></div>
<div class="context-block">
    <div class="box-header">
        <div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl">
            <div class="box-tr">
                <h1 class="context-title">{'Migration Arborescence - Export'|i18n('owexportcontent')}</h1><div class="header-subline"></div>
            </div>
        </div></div></div></div>
    </div>
    
    <div class="box-content">    
        <div class="content-navigation-childlist">
            {if is_set($error)}
                <p class="error">{$error}</p> 
            {/if}
        </div>
    </div>
    
    
    <div class="box-ml"><div class="box-mr"><div class="box-content">        
        <div class="content-navigation-childlist">
            {if is_set($noError)}
                <p>{$noError}</p>
            {else}   
            <form action={'owexportcontent/exportcontent'|ezurl()} method="post">
                <input type="hidden" name="todo" value="exportNode" />
                
                <div class="attribute-item">
                    <label>Entrer le numéro du noeud à partir duquel vous voulez exporter l'arborescence</label>
                    <input type="text" name="rootNode" value="" />
                </div>
                <br />
                <div class="attribute-item">
                    <div>Cochez les options que vous désirées exporter pour chaque object</div><br />
                    <label>Exportez le remote_id</label><input type="checkbox" name="options[]" value="RemoteID" /><br />
                    <label>Exportez la définition de classe</label><input id="class_definition" type="checkbox" name="options[]" value="class" /><br />
                    <div class="hidden option_class">
                        <label>Option pour la définition de classe</label>
                        <select name="class_option">
                        <option value="replace">Replace class</option>
                        <option value="new">New class</option>
                        <option value="extend">Extend class</option>
                        <option value="skip">Skip class</option>
                        </select>
                    <br /></div>
                    <label>Exportez la section</label><input type="checkbox" name="options[]" value="SectionID" /><br />
                    <label>Exportez le propriétaire</label><input type="checkbox" name="options[]" value="OwnerID" /><br />
                    <label>Exportez le sort_order</label><input type="checkbox" name="options[]" value="SortOrder" /><br />
                    <label>Exportez le sort_field</label><input type="checkbox" name="options[]" value="SortField" />
                </div>
                
                <div class="attribute-item">                    
                    <input type="submit" name="export" value="export" />
                </div>
                
            </form>
            {/if}
        </div>
    </div></div></div>
</div>
<script type="text/javascript">
$(document).ready(function() {ldelim}
    if ($('#class_definition').attr('checked')) {ldelim}
        $('.option_class').removeClass('hidden');
    {rdelim}
    
    $('#class_definition').click(function() {ldelim}
        if ($(this).attr('checked')) {ldelim}
            $('.option_class').removeClass('hidden');
        {rdelim} else {ldelim}
            $('.option_class').addClass('hidden');
        {rdelim}
    {rdelim});
{rdelim});
</script>

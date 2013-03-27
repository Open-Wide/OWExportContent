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
            <form action={'owexportcontent/exportclass'|ezurl()} method="post">
                <input type="hidden" name="todo" value="exportClass" />
                
                <div class="attribute-item"><label>Option pour la définition de classe</label>
                <select name="class_option">
                <option value="replace">Replace class</option>
                <option value="new">New class</option>
                <option value="extend">Extend class</option>
                <option value="skip">Skip class</option>
                </select></div><br />
                    
                <div class="attribute-item">                    
                    <input type="submit" name="export" value="export" />
                </div>
                
            </form>
            {/if}
        </div>
    </div></div></div>
</div>

{*?template charset=UTF-8*}
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
		  <a onClick="return confirm('Etes vous sur de vouloir importer ce fichier');" title="import" href={concat('importfile/(file)/', $file)}><img src={'images/import.gif'|ezdesign()} /></a>
		  <a onClick="return confirm('Etes vous sur de vouloir supprimer ce fichier');" title="delete" href={concat('deletefile/(file)/', $file)}><img src={'images/delete.gif'|ezdesign()} /></a>
		</td>
	</tr>
{set $compteur = $compteur|inc()} 
{/foreach}
</table>
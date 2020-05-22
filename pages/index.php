<?php
session_start();
header('Cache-control: max-age='.(60*60*24*365));
if(!isset($_SESSION['id'])) {
	require('notloggedin.html');
	exit();
}
$page_title = "My Flytrap";
require('brand_header.html');
require('search.html');
?>
<div class = "nav-container">
	<div id = 'filter-view'>
		<svg height="12" width="12">
			<line x1="0" y1="1" x2="12" y2="1" style="stroke:rgb(0,0,0);stroke-width:2" />
			<line x1="0" y1="6" x2="12" y2="6" style="stroke:rgb(0,0,0);stroke-width:2" />
			<line x1="0" y1="11" x2="12" y2="11" style="stroke:rgb(0,0,0);stroke-width:2" />
			Sorry, your browser does not support inline SVG.
		</svg>
		<svg height="12" width="12">
			<line x1="1" y1="0" x2="1" y2="12" style="stroke:rgb(0,0,0);stroke-width:2" />
			<line x1="6" y1="0" x2="6" y2="12" style="stroke:rgb(0,0,0);stroke-width:2" />
			<line x1="11" y1="0" x2="11" y2="12" style="stroke:rgb(0,0,0);stroke-width:2" />
			Sorry, your browser does not support inline SVG.
		</svg>
		<div class = "slider" style = "<?php echo isset($_GET['sortby']) ? 'left: 0px' : 'left: 18px'; ?>"></div>
	</div>
	<select onchange = "filterAudio(this.value)" id = 'filter-audio'>
		<option value = "owned">My Audios</option>
		<option value = "shared">Shared Audios</option>
		<option value = "all">All Audios</option>
	</select>
	<button class = "new-button" id = 'new-audio-btn'>
		<img id = 'new-audio-icon' src = 'images/NewAudio.png'>
		<span>New Audio</span>
	</button>
	<button class = "new-button" id = 'new-folder-btn'>
		<img id = 'new-folder-icon' src = "images/NewFolder.png">
		<span>New Folder</span>
	</button>
</div>
<?php

function sortBy($name) {
	if (isset($_GET['sortby'])) {
		switch ($_GET['sortby']) {
			case 'name':
				return " ORDER BY $name";
			case 'time':
				return " ORDER BY time_created DESC";
		}
	}
}

?>
<div class = "file-list-container">
	<ul class = "folders flexbox" style = "<?php echo !isset($_GET['sortby']) ? 'display:flex' : 'display:none'; ?>">
		<?php
		require_once('uniqueid.php');
		# Retrieve folders for this user
		require('../../flytrap_connect.inc.php');
		$q = "SELECT id, folder_name, time_created FROM folders WHERE user_id = {$_SESSION['id']}";
		$r = mysqli_query($dbc, $q);
		while ($row = mysqli_fetch_array($r, MYSQLI_BOTH)) {
			$alphaid = alphaid($row['id'], false, 10);
			echo "<li id = \"folder-{$row['id']}\">
			<a href = 'folders/$alphaid'>{$row['folder_name']}</a>
			<div class = 'customize-btns'>
				<button class = 'rename-folder'><img class = 'grey-circle' src = 'http://cdn.bforborum.com/images/Edit.png'></button>
				<button class = 'delete-folder'><img class = 'grey-circle' src = 'http://cdn.bforborum.com/images/Delete.png'></button>
				<button class = 'share-folder'><img class = 'grey-circle' src = 'http://cdn.bforborum.com/images/register.png'></button>
			</div>
			</li>";
		}
		?>
	</ul>
	<ul class = "files flexbox" style = "<?php echo !isset($_GET['sortby']) ? 'display:flex' : 'display:none'; ?>">
		<?php
		# Retrieve files for this user
		$q = "SELECT id, time_created, file_name FROM audio_files WHERE user_id = {$_SESSION['id']} AND folder_id IS NULL";
		$r = mysqli_query($dbc, $q);
		while ($row = mysqli_fetch_array($r, MYSQLI_BOTH)) {

			$alphaid = alphaid($row['id'], false, 10);
			echo "
			<li id = \"file-{$row['id']}\">
				<a href = 'audio/$alphaid'>
					<img src = 'images/microphone.png'>
					<p>{$row['file_name']}</p>
				</a>
				<div class = 'customize-btns'>
					<button class = 'rename-audio'><img class = 'grey-circle' src = 'http://cdn.bforborum.com/images/Edit.png'></button>
					<button class = 'delete-audio'><img class = 'grey-circle' src = 'http://cdn.bforborum.com/images/Delete.png'></button>
					<button class = 'share-audio'><img class = 'grey-circle' src = 'http://cdn.bforborum.com/images/register.png'></button>
				</div>
			</li>";
		}
		?>
	</ul>
	<table style = "<?php echo isset($_GET['sortby']) ? 'display:block' : 'display:none'; ?>" class = "folders">
		<thead>
			<tr>
				<th><a href = "?sortby=name">Name</a></th>
				<th><a href = "?sortby=time">Date Created</a></th>
		</thead>
		<tbody>
			<?php
			# Retrieve files for this user
			$q = "SELECT id, folder_name, time_created FROM folders WHERE user_id = {$_SESSION['id']}";
			$q .= sortBy('folder_name');
			$r = mysqli_query($dbc, $q);
			while ($row = mysqli_fetch_array($r, MYSQLI_BOTH)) {
				echo "
				<tr>
					<td>{$row['folder_name']}</td>
					<td>{$row['time_created']}</td>
				</tr>";
			}
			?>
		</tbody>
	</table>
	<table class = "files" style = "<?php echo isset($_GET['sortby']) ? 'display:block' : 'display:none'; ?>">
		<thead>
			<tr>
				<th><a href = "?sortby=name">Name</a></th>
				<th><a href = "?sortby=time">Date Created</a></th>
			</tr>
		</thead>
		<tbody>
			<?php
			# Retrieve files for this user
			$q = "SELECT id, file_name, time_created FROM audio_files WHERE user_id = {$_SESSION['id']} AND folder_id IS NULL";
			$q .= sortBy('file_name');
			$r = mysqli_query($dbc, $q);
			while ($row = mysqli_fetch_array($r, MYSQLI_BOTH)) {
				echo "
				<tr>
					<td>{$row['file_name']}</td>
					<td>{$row['time_created']}</td>
				</tr>";
			}
			?>
		</tbody>
	</table>
</div>
<div class = "plus-container">
	<a id = "add-audio">+</a>
	<div id = 'plus-between' style="
    width: 40px;
    height: 50px;
		background: none;
		display: none;
    position: absolute;
		"></div>
</div>
<div style = "display: none" class = "new-file-box-container">
	<!-- Record audio -->
	<div>
		<button class = "audio-option">Record directly</button><br>
		<input id = 'volume' type = 'range' min = '1' max = '10'>
	</div>
	<!-- Vertical line -->
	<span class = "vertical-line"></span>
	<!-- Upload file -->
	<form id = 'upload-form' action = '' enctype = 'multipart/form-data'>
		<label class="audio-option custom-file-upload">
	    <input multiple id = "audio-files" name = "file" type="file"/>
    	Upload mp3, wav, m4a
		</label>
		<img height = "200" id = 'upload-file-arrow' src = "images/UploadFileArrow.png">
		<progress style = 'display:none' value = "0" max = "100" id = 'upload-file-progress-bar'></progress>
	</form>
</div>
<div style = "display: none" class = "action-container rename-container">
	<img src = "images/Exit.png">
	<input type = "text" value = "">
	<input type = "button" value = "Rename" onclick = "renameAudioFile(<?php echo $_SESSION['id']; ?>, this.parentElement.id)">
</div>
<div style = "display: none" class = "action-container share-container">
	<img src = "images/Exit.png">
	<input type = "text" placeholder = "Insert recipient's email">
	<input onclick = "shareAudioFile()" type = "button" value = "Share">
</div>
<div style = "display: none" class = "action-container delete-container">
	<p>Are you sure you want to <strong>permanently</strong> delete this file? You will not be able to get it back.</p>
	<input type = "button" value = "Cancel" onclick = "this.parentElement.style.display = 'none';">
	<input type = "button" value = "Delete" onclick = "deleteAudioFile(<?php echo $_SESSION['id']; ?>, this.parentElement.id.substring(17))">
</div>
<div style = "display: none" class = "action-container rename-container">
	<img src = "images/Exit.png">
	<input type = "text" value = "">
	<input type = "button" value = "Rename" onclick = "renameFolder(<?php echo $_SESSION['id']; ?>, this.parentElement.id)">
</div>
<div style = "display: none" class = "action-container share-container">
	<img src = "images/Exit.png">
	<input type = "text" placeholder = "Insert recipient's email">
	<input onclick = "shareFolder()" type = "button" value = "Share">
</div>
<div style = "display: none" class = "action-container delete-container">
	<p>Are you sure you want to <strong>permanently</strong> delete this folder? You will not be able to get it back.</p>
	<input type = "button" value = "Cancel" onclick = "this.parentElement.style.display = 'none';">
	<input type = "button" value = "Delete" onclick = "deleteFolder(<?php echo $_SESSION['id']; ?>, this.parentElement.id.substring(20))">
</div>
<div class = "new-media-btn-container"></div>
<div class = "status-container" style = "display: none"></div>
<?php
include('footer.html');
?>
</div>
<script src = "scripts/script.js"></script>
<script src = "scripts/minified/popupBox.min.js"></script>
<script src = "scripts/minified/filters.min.js"></script>
<script src = "scripts/minified/media.min.js"></script>
<script src = "scripts/inPopups.js"></script>
</body>
</html>

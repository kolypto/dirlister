<table>
    <caption>
        <a href="/">root: </a>
        <?php foreach ($path=explode('/',trim($currentPath,'/')) as $i => $d): ?>
            / <a href="<?=str_repeat('../', count($path)-$i-1);?>"><?= htmlspecialchars($d); ?></a>
        <?php endforeach; ?>
    </caption>
    <thead><tr>
        <th>Name</th>
        <th>Size</th>
        <th>Time</th>
    </tr></thead>
    <tbody>

    <?php foreach($files as $file): ?>
        <tr class="<?=$file->is_dir? 'folder' : 'file';?>">
            <td>
                <a class="name" href="<?=htmlspecialchars($file->url);?>"><?=htmlspecialchars($file->name);?></a>
                <p class="description"><?=htmlspecialchars($file->comment);?></p>
            </td>
            <td class="size"><?=$file->size;?></td>
            <td class="date"><?=$file->mtime;?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

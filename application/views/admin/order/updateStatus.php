<?php //debug($data); ?>
<h2>Update Status Order</h2>

<p>Are you want to update id-<b>"<?php echo $data['id']; ?>"</b>`s status?</p>

<form method="post">
    <input style="font-weight: bold;cursor: pointer" type="submit" name="submit" value="UPDATE">
    <a style="margin-left: 20px;font-weight: bold;cursor: pointer"  href="/admin/order">CANCEL</a>
</form>

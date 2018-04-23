
<?php
include "mylib.php";

if (isset($_GET["transrkrip"])) {
  printValidasi($_GET["transrkrip"]);
}
?>
<form method="get">
<input type="hidden" name="page_id" value="<?php echo $_GET["page_id"]; ?>">
<label> Input Text Transkrip </label>
<textarea rows="15" cols="40" name="transrkrip"><?php echo isset($_GET["transrkrip"]) ? $_GET["transrkrip"] : ""; ?></textarea>
<input type="submit" value="Submit" name="submit">
</form>
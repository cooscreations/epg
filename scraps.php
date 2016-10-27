<form id="example" name="example">
        <select id="sensor" onchange="updateText('sensor')">
        <option value="J">J</option>
        <option value="K">K</option>
    </select>

    <select id="voltage" onchange="updateText('voltage')">
        <option value="120V">120V</option>
        <option value="240V">240V</option>
    </select>

    <br />
    <input type="text" value="" id="sensorText" /> <input type="text" value="" id="voltageText" />
</form>

<script type="text/javascript">

function updateText(type) { 
 var id = type+'Text';
 document.getElementById(id).value = document.getElementById(type).value;
}
</script>
<!-- popmsg.php -->
<div id="popmsg" class="popmsg"></div>

<div id="specialDayPopup" class="popup">
    <div class="popup-content">
        <span class="btnAnnClose">&times;</span>
        <h2 style="text-align: center;">Special Day</h2>
        <form id="annForm">
            <div class="form-group">
                <label>Date:</label>
                <input type="date" id="annDate" required>
            </div>
            <div class="form-group">
                <label>Event Name:</label>
                <input type="text" id="eventName" required>
            </div>
            <button type="button" class="btnAnnSubmit" onclick="submitSpecialDay()">Submit</button>
        </form>
    </div>
</div>


<div id="popmsg" class="popmsg"></div>

<div id="specialDayPopup" class="popup">
    <div class="popup-content">
    <span class="btnAnnClose">&times;</span>
        <h2 style="text-align: center;">Announcement</h2>        
        <form id="annForm" action="postAnnDB.php" method="POST">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="announcement_tittle" required>
            </div>
            <div class="form-group">
                <label>Content:</label>
                <textarea name="announcement_content" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="announcement_date" required>
            </div>
            <button type="submit" class="btnAnnSubmit">Submit</button>
        </form>
    </div>
</div>
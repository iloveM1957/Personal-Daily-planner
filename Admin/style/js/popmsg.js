// faq.js
function initPostButton() {
    // 1. pen pop-up window event
    document.getElementById('postButton').addEventListener('click', function() {
        document.getElementById('popmsg').style.display = 'block';
        document.getElementById('specialDayPopup').style.display = 'block';
    });

    // 2. close pop-up window event (close button)
    document.querySelector('.btnAnnClose').addEventListener('click', function() {
        document.getElementById('popmsg').style.display = 'none';
        document.getElementById('specialDayPopup').style.display = 'none';
    });

    // 3. Click outside to close the pop-up window
    document.getElementById('popmsg').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('popmsg').style.display = 'none';
            document.getElementById('specialDayPopup').style.display = 'none';
        }
    });

    // 4. form submission event
    document.getElementById('annForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const date = document.getElementById('annDate').value;
        const eventName = document.getElementById('eventName').value;
        console.log('Submitted:', { date, eventName });
        this.reset();
        document.getElementById('popmsg').style.display = 'none';
        document.getElementById('specialDayPopup').style.display = 'none';
    });



    document.getElementById('annForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = {
            announcement_tittle: this.announcement_tittle.value,
            announcement_content: this.announcement_content.value,
            announcement_date: this.announcement_date.value
        };
    
        $.ajax({
            url: './function/postAnnDB.php', // Make sure the path is correct
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    manageAnn(); // Refresh the management page
                    document.getElementById('popmsg').style.display = 'none';
                    document.getElementById('specialDayPopup').style.display = 'none';
                } else {
                    alert(response.error || 'Failed to add announcement');
                }
            },
            error: function(xhr) {
                alert('Request failed: ' + xhr.statusText);
            }
        });
    });
}

// initialization function call
initPostButton();
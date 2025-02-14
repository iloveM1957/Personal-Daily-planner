<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Management</title>
    <link rel="stylesheet" href="./style/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container_FAQs">
        <h2 class="faq-header">Frequently Asked Questions</h2>
        <div class="button-container">
            <button onclick="openAddModal()">Add FAQ</button>
            <button onclick="toggleDelete()">Delete FAQ</button>
            <button onclick="toggleEdit()">Edit FAQ</button>
        </div>
        <div id="faqList">
            <!-- FAQ列表动态加载 -->
        </div>
    </div>

    <!-- 添加FAQ的模态框 -->
    <div id="addFaqModal" class="modal">
        <div class="modal-content">
            <h3>Add FAQ</h3>
            <label>Question:</label>
            <input type="text" id="newQuestion"><br>
            <label>Solution:</label>
            <textarea id="newSolution"></textarea><br>
            <button onclick="addFaq()">Done</button>
            <button onclick="closeAddModal()">Cancel</button>
        </div>
    </div>

    <script>
        let isDeleteEnabled = false;
        let isEditEnabled = false;

        // 页面加载时获取FAQ列表
        $(document).ready(function() {
            fetchFAQs();
        });

        function fetchFAQs() {
            $.ajax({
                url: "./function/faqDB.php",
                method: "POST",
                data: { action: 'getFAQs' },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        renderFAQs(response.faqs);
                    } else {
                        alert('Failed to load FAQs: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    alert('Request error: ' + error);
                }
            });
        }


        function renderFAQs(faqs) {
            let faqList = document.getElementById("faqList");
            faqList.innerHTML = "";
            faqs.forEach((faq) => {
        let faqItem = document.createElement("div");
        faqItem.className = "faq-item";
        faqItem.innerHTML = `
    <div class="accordion-header">
        <button class="accordion">${faq.question}</button>
        <div class="action-buttons">
            ${isEditEnabled ? `<button class="edit-btn" onclick="openEditModal(${faq.faq_id})">✏️</button>` : ''}
            ${isDeleteEnabled ? `<button class="delete-btn" onclick="deleteFaq(${faq.faq_id})">❌</button>` : ''}
        </div>
    </div>
    <div class="panel">${faq.solution}</div>
`;
        faqList.appendChild(faqItem);
    });

            // 绑定手风琴效果
            // 绑定手风琴效果
            let acc = document.getElementsByClassName("accordion");
            for (let i = 0; i < acc.length; i++) {
                acc[i].addEventListener("click", function(e) {
                    // Only trigger if not clicking action buttons
                    if (!e.target.closest('.action-buttons')) {
                    this.classList.toggle("active");
                    let panel = this.parentElement.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    }
                }
            });
        }
        }

        // 添加FAQ相关函数
        function openAddModal() {
            document.getElementById("addFaqModal").style.display = "block";
        }

        function closeAddModal() {
            document.getElementById("addFaqModal").style.display = "none";
            document.getElementById("newQuestion").value = "";
            document.getElementById("newSolution").value = "";
        }

        function addFaq() {
            const question = $("#newQuestion").val();
            const solution = $("#newSolution").val();

            if (!question || !solution) {
                alert("Please fill in both Question and Solution.");
                return;
            }

            $.ajax({
                url: "./function/faqDB.php",
                method: "POST",
                data: {
                    action: "addFaq",
                    question: question,
                    solution: solution
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        fetchFAQs();
                        closeAddModal();
                        alert("FAQ added successfully!");
                    } else {
                        alert("Failed to add FAQ: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                }
            });
        }


        // 删除FAQ函数
        function deleteFaq(faq_id) {
            if (!confirm("Are you sure you want to delete this FAQ?")) return;

            $.ajax({
                url: "./function/faqDB.php",
                method: "POST",
                data: {
                    action: "deleteFaq",
                    faq_id: faq_id
                },
                dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            fetchFAQs();
                            alert("FAQ deleted successfully!");
                        } else {
                            alert("Failed to delete: " + response.message);
                        }
                    }
            });
        }


        // 编辑FAQ函数
        function openEditModal(faq_id) {
            let currentFaq = $(`#faq-${faq_id} .accordion`).text().trim();
            let currentSolution = $(`#faq-${faq_id} .panel`).text().trim();

            let newQuestion = prompt("Edit Question", currentFaq);
            let newSolution = prompt("Edit Solution", currentSolution);

            if (newQuestion && newSolution) {
                $.ajax({
                    url: "./function/faqDB.php",
                    method: "POST",
                    data: {
                        action: "editFaq",
                        faq_id: faq_id,
                        question: newQuestion,
                        solution: newSolution
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            fetchFAQs();
                            alert("FAQ updated successfully!");
                        } else {
                            alert("Failed to update: " + response.message);
                        }
                    }
                });
            }
        }

        // 切换删除/编辑模式
        function toggleDelete() {
            isDeleteEnabled = !isDeleteEnabled;
            fetchFAQs(); // Re-fetch FAQs
        }

        function toggleEdit() {
            isEditEnabled = !isEditEnabled;
            fetchFAQs(); // Re-fetch FAQs
        }
    </script>
</body>
</html>
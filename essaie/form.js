document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("feedbackForm");

    const result = document.getElementById("result");

    form.addEventListener("submit", async (e) => {

        e.preventDefault();

        const data = {

            name: document.getElementById("name").value,

            phone: document.getElementById("phone").value,

            email: document.getElementById("email").value,

            comment: document.getElementById("comment").value

        };

        try {

            const response = await fetch("api/form.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(data)

            });

            const resultData = await response.json();

            if (resultData.success) {

                result.innerHTML = `
                    <div class="alert alert-success mt-3">

                        <strong>Успешно!</strong><br>

                        Логин: ${resultData.login}<br>

                        Пароль: ${resultData.password}

                    </div>
                `;

                form.reset();

            } else {

                result.innerHTML = `
                    <div class="alert alert-danger mt-3">
                        ${resultData.errors.join("<br>")}
                    </div>
                `;
            }

        } catch (error) {

            result.innerHTML = `
                <div class="alert alert-danger mt-3">
                    Ошибка сервера
                </div>
            `;
        }
    });

});

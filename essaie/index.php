<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Drupal Coder</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <header class="py-4 border-bottom">

        <div class="container">

            <div class="d-flex justify-content-between align-items-center">

                <div class="logo">
                    <h2 class="m-0">Drupal Coder</h2>
                </div>

                <nav>

                    <ul class="nav">

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Главная
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Услуги
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Контакты
                            </a>
                        </li>

                    </ul>

                </nav>

            </div>

        </div>

    </header>

    <!-- ===================================================== -->
    <!-- MAIN -->
    <!-- ===================================================== -->

    <main>

        <!-- ================================================= -->
        <!-- HERO SECTION -->
        <!-- ================================================= -->

        <section class="py-5 bg-light">

            <div class="container">

                <div class="row align-items-center">

                    <div class="col-12 col-lg-6">

                        <h1 class="display-5 fw-bold mb-4">
                            Поддержка сайтов <br>
                            Drupal
                        </h1>

                        <p class="lead">
                            Профессиональная поддержка и развитие
                            ваших WEB-проектов.
                        </p>

                    </div>

                    <div class="col-12 col-lg-6 text-center">

                        <img
                            src="img/hero.png"
                            alt="Hero"
                            class="img-fluid"
                        >

                    </div>

                </div>

            </div>

        </section>

        <!-- ================================================= -->
        <!-- FORM SECTION -->
        <!-- ================================================= -->

        <section id="form-section" class="py-5">

            <div class="container">

                <div class="row">

                    <!-- LEFT COLUMN -->

                    <div
                        id="text"
                        class="col-12 col-md-6 mb-5 mb-md-0"
                    >

                        <div class="text1 mb-4">

                            <h2>
                                Оставить заявку <br>
                                на поддержку сайта
                            </h2>

                        </div>

                        <div class="text2 mb-4">

                            <p>
                                Срочно нужна поддержка сайта?
                                Ваша команда не успевает
                                справиться самостоятельно
                                или предыдущий подрядчик
                                не справился с работой?
                            </p>

                            <p>
                                Тогда вам стоит обратиться к нам!
                                Просто оставьте заявку и наш
                                менеджер свяжется с вами.
                            </p>

                        </div>

                        <div class="contacts">

                            <ul class="list-unstyled">

                                <li class="mb-3">

                                    <a
                                        href="tel:88002222673"
                                        class="text-decoration-none"
                                    >
                                        8 800 222-26-73
                                    </a>

                                </li>

                                <li>

                                    <a
                                        href="mailto:info@drupal-coder.ru"
                                        class="text-decoration-underline"
                                    >
                                        info@drupal-coder.ru
                                    </a>

                                </li>

                            </ul>

                        </div>

                    </div>

                    <!-- RIGHT COLUMN -->

                    <div
                        id="form"
                        class="col-12 col-md-6"
                    >

                        <form
                            id="feedbackForm"
                            action="api/form.php"
                            method="POST"
                            class="row g-3"
                        >

                            <!-- NAME -->

                            <div class="col-12">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    placeholder="Ваше имя"
                                    required
                                >

                            </div>

                            <!-- PHONE -->

                            <div class="col-12">

                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    placeholder="Телефон"
                                >

                            </div>

                            <!-- EMAIL -->

                            <div class="col-12">

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="E-mail"
                                    required
                                >

                            </div>

                            <!-- COMMENT -->

                            <div class="col-12">

                                <textarea
                                    class="form-control"
                                    id="comment"
                                    name="comment"
                                    rows="5"
                                    placeholder="Ваш комментарий"
                                    required
                                ></textarea>

                            </div>

                            <!-- CHECKBOX -->

                            <div class="col-12">

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="agree"
                                        required
                                    >

                                    <label
                                        class="form-check-label"
                                        for="agree"
                                    >
                                        Отправляя заявку,
                                        я даю согласие
                                        на обработку
                                        персональных данных
                                    </label>

                                </div>

                            </div>

                            <!-- BUTTON -->

                            <div class="col-12">

                                <button
                                    id="submitBtn"
                                    type="submit"
                                    class="btn btn-primary w-100"
                                >
                                    ОТПРАВИТЬ
                                </button>

                            </div>

                        </form>

                        <!-- AJAX RESULT -->

                        <div
                            id="result"
                            class="mt-4"
                        ></div>

                    </div>

                </div>

            </div>

        </section>

    </main>

    <!-- ===================================================== -->
    <!-- FOOTER -->
    <!-- ===================================================== -->

    <footer class="py-4 bg-dark text-white">

        <div class="container text-center">

            <p class="m-0">
                © 2026 Drupal Coder
            </p>

        </div>

    </footer>

    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    <!-- JS -->

    <script src="js/form.js"></script>

</body>

</html>

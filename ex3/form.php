<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Lab 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1"
          crossorigin="anonymous">
</head>

<body>
<div class="container">
    <h1 class="text-center m-5">Lab 3</h1>

    <div class="container-sm theme-list py-3 pl-0 mb-3">
        <div class="d-flex flex-column align-items-center">

            <form class="d-block p-2" action="index.php" method="POST">

                <div class="form-floating mb-3">
                    <input class="form-control" name="fio" type="text" placeholder="FirstName LastName" id="fio">
                    <label for="fio" class="form-label">Ваше имя</label>
                </div>

                <div class="form-floating mb-3">
                    <input class="form-control" name="email" type="email" placeholder="name@example.com" id="email">
                    <label for="email" class="form-label">Ваш email</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Дата рождения
                        <input class="form-control form-control-md info" type="date" name="year" autocomplete="off">
                    </label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Пол</label>
                    <br>
                    <input class="form-check-input" type="radio" name="sex" id="sex1" value="0" checked> Male
                    <input class="form-check-input" type="radio" name="sex" id="sex2" value="1"> Female
                </div>

                <div class="mb-3">
                    <label class="form-label">Число конечностей</label>
                    <br>
                    <input class="form-check-input" type="radio" name="limb" id="limb1" value="2" checked>
                    <label class="form-check-label">2</label>
                    <input class="form-check-input" type="radio" name="limb" id="limb2" value="4">
                    <label class="form-check-label">4</label>
                    <input class="form-check-input" type="radio" name="limb" id="limb3" value="6">
                    <label class="form-check-label">6</label>
                    <input class="form-check-input" type="radio" name="limb" id="limb4" value="8">
                    <label class="form-check-label">8</label>
                    <input class="form-check-input" type="radio" name="limb" id="limb5" value="10">
                    <label class="form-check-label">10</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Сверхсособности</label>
                    <select class="form-select" name="abilities[]" multiple>
                        <option value="immort">Бессмертие</option>
                        <option value="wall">Прохождение сквозь стены</option>
                        <option value="levit">Левитация</option>
                        <option value="invis">Невидимость</option>
                    </select>
                </div>

                <div class="mb-3 form-floating">
                    <textarea class="form-control" name="text" placeholder="Биография" style="height: 100px; width: 420px;"></textarea>
                    <label class="form-label">Биография</label>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="accept" value="1">
                    <label class="form-check-label">Даю согласие на всё!</label>
                </div>

                <div class="d-grid gap-2 col-6 mx-auto">
                    <button class="btn btn-light" type="submit" value="Отправить">Отправить!</button>
                </div>

            </form>

        </div>
    </div>

</div>
<footer>
    <h2 class="text-center m-3">
        Тиунов 21/2
    </h2>
</footer>
</body>


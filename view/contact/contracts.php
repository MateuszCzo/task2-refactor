<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klient - Szczegóły</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        svg{
            fill: black;
        }
    </style>
</head>
<body class="container mt-4">
    <h1 class="mb-4">Kontrakty</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa przedsiebiorcy</th>
                <?php if ($action == 5): ?>
                    <th>Kwota</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contracts as $contract): ?>
                <tr>
                    <td><?= htmlspecialchars($contract->getId()) ?></td>
                    <td><?= htmlspecialchars($contract->getBusinessName()) ?></td>
                    <?php if ($action == 5): ?>
                        <td><?= htmlspecialchars($contract->getAmount()) ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

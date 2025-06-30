<?php
require_once '../config/init.php';
requireRole('student');

$user = getUser();

// Ambil ID material dari parameter
$material_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($material_id <= 0) {
    header("Location: dashboard.php?error=invalid_material_id");
    exit();
}

// Query untuk mengambil data material
try {
    $stmt = $db->prepare("SELECT m.*, u.full_name as teacher_name 
                          FROM materials m 
                          LEFT JOIN users u ON m.teacher_id = u.id 
                          WHERE m.id = ?");
    $stmt->execute([$material_id]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$material) {
        header("Location: dashboard.php?error=material_not_found");
        exit();
    }

    // Path file berdasarkan field file_path di database
    $file_path = '../' . $material['file_path'];

    if (!file_exists($file_path)) {
        header("Location: dashboard.php?error=file_not_found");
        exit();
    }

    // Ambil ekstensi file
    $file_extension = strtolower(pathinfo($material['file_path'], PATHINFO_EXTENSION));

    // Daftar ekstensi yang bisa di-preview
    $previewable_extensions = ['pdf', 'docx', 'pptx', 'jpg', 'jpeg', 'png', 'txt'];

    // Jika tidak bisa di-preview, force download
    if (!in_array($file_extension, $previewable_extensions)) {
        header("Location: utils/file-downloader.php?id=" . $material_id);
        exit();
    }
} catch (PDOException $e) {
    die('Database Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($material['title']); ?> - E-Learning SMP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- PDF.js untuk preview PDF -->
    <?php if ($file_extension === 'pdf'): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js" integrity="sha512-ml/QKfG3+Yes6TwOzQb7aCNtJF4PUyha6R3w8pSTo/VJSywl7ZreYvvtUso7fKevpsI+pYVVwnu82YO0q3V6eg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <style>
            #pdf-controls {
                background-color: #f3f4f6;
                padding: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            #pdf-render {
                width: 100%;
                border: 1px solid #e5e7eb;
            }
        </style>
    <?php endif; ?>
</head>

<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <h1 class="text-xl font-bold"><?php echo htmlspecialchars($material['title']); ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                    <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Material Info -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($material['title']); ?></h2>
                        <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($material['description']); ?></p>
                        <div class="mt-4 flex flex-wrap gap-4">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-book mr-1"></i> <?php echo htmlspecialchars($material['subject']); ?>
                            </span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-user-tie mr-1"></i> <?php echo htmlspecialchars($material['teacher_name']); ?>
                            </span>
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-calendar-alt mr-1"></i> <?php echo date('d M Y', strtotime($material['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    <a href="../utils/file-downloader.php?id=<?php echo $material_id; ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-download mr-2"></i>Unduh
                    </a>
                </div>
            </div>

            <!-- File Preview -->
            <div class="p-4">
                <?php if ($file_extension === 'pdf'): ?>
                    <!-- PDF Viewer -->
                    <div id="pdf-viewer-container" class="w-full overflow-y-scroll" style="height: 70vh;">
                        <div id="pdf-controls" class="bg-gray-100 p-2 flex items-center justify-between">
                            <div>
                                <button id="prev-page" class="bg-blue-600 text-white px-3 py-1 rounded mr-2">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <span id="page-num" class="font-medium">Halaman: 1</span>
                                <span id="page-count" class="text-gray-600 ml-1">/ 1</span>
                                <button id="next-page" class="bg-blue-600 text-white px-3 py-1 rounded ml-2">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                            <div>
                                <span class="text-gray-600 mr-2">Zoom:</span>
                                <button id="zoom-out" class="bg-gray-200 px-3 py-1 rounded mr-1">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <button id="zoom-in" class="bg-gray-200 px-3 py-1 rounded">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>
                        <canvas id="pdf-render" class="w-full border border-gray-200"></canvas>
                    </div>
                    <script>
                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

                        const url = '../<?php echo $material['file_path']; ?>';

                        let pdfDoc = null,
                            pageNum = 1,
                            pageRendering = false,
                            pageNumPending = null,
                            currentZoomScale = 1.0,
                            canvas = document.getElementById('pdf-render'),
                            ctx = canvas.getContext('2d');

                        function renderPage(num) {
                            pageRendering = true;
                            pdfDoc.getPage(num).then(function(page) {
                                const containerWidth = canvas.clientWidth;
                                const viewportOriginal = page.getViewport({
                                    scale: 1.0
                                });

                                const baseScale = containerWidth / viewportOriginal.width;
                                const finalScale = baseScale * currentZoomScale;
                                const viewport = page.getViewport({
                                    scale: finalScale
                                });

                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                const renderContext = {
                                    canvasContext: ctx,
                                    viewport: viewport
                                };

                                const renderTask = page.render(renderContext);

                                renderTask.promise.then(function() {
                                    pageRendering = false;
                                    if (pageNumPending !== null) {
                                        renderPage(pageNumPending);
                                        pageNumPending = null;
                                    }
                                });
                            });

                            document.getElementById('page-num').textContent = 'Halaman: ' + num;
                        }

                        function queueRenderPage(num) {
                            if (pageRendering) {
                                pageNumPending = num;
                            } else {
                                renderPage(num);
                            }
                        }

                        function onPrevPage() {
                            if (pageNum <= 1) return;
                            pageNum--;
                            queueRenderPage(pageNum);
                        }

                        function onNextPage() {
                            if (pageNum >= pdfDoc.numPages) return;
                            pageNum++;
                            queueRenderPage(pageNum);
                        }

                        // Perbaikan pada fungsi Zoom
                        function zoomIn() {
                            currentZoomScale += 0.25;
                            queueRenderPage(pageNum);
                        }

                        function zoomOut() {
                            if (currentZoomScale <= 0.5) return;
                            currentZoomScale -= 0.25;
                            queueRenderPage(pageNum);
                        }

                        // Initialize PDF.js
                        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
                            pdfDoc = pdfDoc_;
                            document.getElementById('page-count').textContent = '/ ' + pdfDoc.numPages;
                            renderPage(pageNum);
                        }).catch(function(error) {
                            console.error('Error loading PDF: ', error);
                            document.getElementById('pdf-viewer-container').innerHTML = `
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                                    <p class="font-medium">Gagal memuat dokumen PDF.</p>
                                    <p>Silakan unduh file untuk melihat isinya.</p>
                                </div>
                            `;
                        });

                        // Event listeners
                        document.getElementById('prev-page').addEventListener('click', onPrevPage);
                        document.getElementById('next-page').addEventListener('click', onNextPage);
                        document.getElementById('zoom-in').addEventListener('click', zoomIn);
                        document.getElementById('zoom-out').addEventListener('click', zoomOut);

                        // Event listener untuk resize window (sudah benar)
                        let resizeTimer;
                        window.addEventListener('resize', function() {
                            clearTimeout(resizeTimer);
                            resizeTimer = setTimeout(function() {
                                if (pdfDoc) {
                                    renderPage(pageNum);
                                }
                            }, 250);
                        });
                    </script>

                <?php elseif (in_array($file_extension, ['docx', 'pptx'])): ?>
                    <!-- Office File Viewer -->
                    <div class="w-full" style="height: 70vh;">
                        <?php
                        // Buat URL absolut untuk file
                        $file_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/$material[file_path]";
                        ?>
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?php echo urlencode($file_url); ?>"
                            width="100%" height="100%" frameborder="0" class="border border-gray-200"></iframe>
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Jika dokumen tidak muncul, silakan <a href="../utils/file-downloader.php?id=<?php echo $material_id; ?>" class="text-blue-600 hover:underline">unduh file</a> untuk melihatnya.</p>
                        </div>
                    </div>

                <?php elseif (in_array($file_extension, ['jpg', 'jpeg', 'png'])): ?>
                    <!-- Image Viewer -->
                    <div class="flex justify-center">
                        <img src="../<?php echo $material['file_path']; ?>"
                            alt="<?php echo htmlspecialchars($material['title']); ?>"
                            class="max-w-full max-h-screen border border-gray-200">
                    </div>

                <?php elseif ($file_extension === 'txt'): ?>
                    <!-- Text File Viewer -->
                    <div class="bg-gray-50 p-4 rounded border border-gray-200" style="max-height: 70vh; overflow-y: auto;">
                        <pre class="whitespace-pre-wrap font-mono text-gray-800"><?php
                                                                                    echo htmlspecialchars(file_get_contents($file_path));
                                                                                    ?></pre>
                    </div>

                <?php else: ?>
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                        <p class="font-medium">File tidak dapat ditampilkan sebagai preview.</p>
                        <p>Silakan unduh file untuk melihat isinya.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
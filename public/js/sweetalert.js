$(document).ready(function () {
    // SweetAlert for success and error messages from hidden fields
    var successMessage = $("#success-message").val();
    var errorMessage = $("#error-message").val();

    if (successMessage) {
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: successMessage,
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: errorMessage,
        });
    }

    // SweetAlert for delete confirmation
    $(".btn-delete").on("click", function (e) {
        e.preventDefault();
        var form = $(this).closest("form");

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

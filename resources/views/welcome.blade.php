<form id="uploadForm" action="/file/upload" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file">
    <button type="submit">Загрузить</button>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#uploadForm').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var token = form.find('input[name="_token"]').val();
            var formData = new FormData(this);

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                },
                error: function(xhr, status, error) {
                }
            });
        });
    });

    import Echo from 'laravel-echo';

    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001',
    });

    window.Echo.channel('row-channel')
        .listen('RowCreated', (e) => {
            console.log('Новая запись создана!', e.record);
        });
</script>

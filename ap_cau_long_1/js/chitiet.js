function updateQuantity(change) {
            const quantityInput = document.getElementById('productQuantity');
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity += change;
            if (currentQuantity < 1) {
                currentQuantity = 1;
            }
            quantityInput.value = currentQuantity;
        }

        function changeImage(newSrc, clickedThumbnail) {
            document.getElementById('mainProductImage').src = newSrc;

            const thumbnails = document.querySelectorAll('.thumbnail-gallery img');
            thumbnails.forEach(thumb => thumb.classList.remove('active'));

            clickedThumbnail.classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            const firstThumbnail = document.querySelector('.thumbnail-gallery img');
            if (firstThumbnail) {
                firstThumbnail.classList.add('active');
            }
        });
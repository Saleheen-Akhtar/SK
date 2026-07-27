(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation for delete buttons
        document.addEventListener('click', function(e) {
            var delBtn = e.target.closest('.sk-btn-del');
            if (delBtn) {
                e.preventDefault();
                var repRow = delBtn.closest('.sk-rep-row');
                if (repRow) {
                    repRow.remove();
                }
            }
        });

        // Add Pair button
        var addPairBtn = document.getElementById('sk-add-hero-pair');
        if (addPairBtn) {
            addPairBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var wrap = document.getElementById('hero-pairs-wrap');
                if (!wrap) return;
                var d = document.createElement('div');
                d.className = 'sk-rep-row';
                d.style.display = 'grid';
                d.style.gridTemplateColumns = '1fr 1fr auto';
                d.style.gap = '8px';
                d.style.alignItems = 'center';
                d.style.padding = '10px 14px';
                d.innerHTML = '<input type="text" name="hero_pair_from[]" placeholder="From (struggle)" style="width:100%;box-sizing:border-box" />' +
                              '<input type="text" name="hero_pair_to[]" placeholder="To (transformation)" style="width:100%;box-sizing:border-box" />' +
                              '<button type="button" class="sk-btn-del" style="position:static;font-size:10px;padding:4px 8px">✕</button>';
                wrap.appendChild(d);
            });
        }

        // Add Pillar button
        var addPillarBtn = document.getElementById('sk-add-pillar');
        if (addPillarBtn) {
            var skT = {
                pillar: '<div class="sk-rep-row"><h4>Pillar</h4><button type="button" class="sk-btn-del">Remove</button><div class="sk-row"><label>No.</label><input type="text" name="pillar_num[]" value="" /></div><div class="sk-row"><label>Title</label><input type="text" name="pillar_title[]" value="" /></div><div class="sk-row"><label>Desc</label><textarea name="pillar_desc[]" rows="2" style="width:100%;box-sizing:border-box"></textarea></div><div class="sk-row"><label>Image URL</label><input type="text" name="pillar_image[]" value="" /></div><div class="sk-row"><label>Image Position (CSS)</label><input type="text" name="pillar_img_position[]" value="" /></div></div>'
            };

            addPillarBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var wrap = document.getElementById('pillars-wrap');
                if (!wrap) return;
                var div = document.createElement('div');
                div.innerHTML = skT.pillar;
                wrap.appendChild(div.firstElementChild);
            });
        }
    });
})();

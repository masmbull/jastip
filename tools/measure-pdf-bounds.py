"""
Measure text/graphics bounds inside a rendered invoice PDF against the
A4 content box (page width minus the 10mm left/right margins).

Usage: python tools/measure-pdf-bounds.py <pdf> [<pdf> ...]
"""
import sys
import pymupdf

MM = 72 / 25.4  # points per mm

for path in sys.argv[1:]:
    doc = pymupdf.open(path)
    print(path)
    for pno in range(doc.page_count):
        page = doc[pno]
        W, H = page.rect.width, page.rect.height
        left_limit = 10 * MM
        right_limit = W - 10 * MM

        clipped_right = []
        clipped_left = []
        max_right = 0.0
        min_left = W

        for block in page.get_text('dict')['blocks']:
            if block['type'] != 0:
                continue
            for line in block['lines']:
                for span in line['spans']:
                    x0, y0, x1, y1 = span['bbox']
                    text = span['text'].strip()
                    if not text:
                        continue
                    max_right = max(max_right, x1)
                    min_left = min(min_left, x0)
                    if x1 > right_limit + 0.5:
                        clipped_right.append((round(x1, 1), round(right_limit, 1), text))
                    if x0 < left_limit - 0.5:
                        clipped_left.append((round(x0, 1), round(left_limit, 1), text))

        print(f"  page {pno+1}: size {W:.1f}x{H:.1f}pt  content box {left_limit:.1f}..{right_limit:.1f}")
        print(f"    text leftmost  : {min_left:.1f}")
        print(f"    text rightmost : {max_right:.1f}  (limit {right_limit:.1f}, overflow {max_right - right_limit:+.1f})")
        if clipped_right:
            print(f"    OVERFLOW RIGHT ({len(clipped_right)} spans):")
            for x1, lim, text in sorted(clipped_right, reverse=True)[:8]:
                print(f"      x1={x1} (+{x1 - lim:.1f}) {text!r}")
        if clipped_left:
            print(f"    OVERFLOW LEFT ({len(clipped_left)} spans):")
            for x0, lim, text in sorted(clipped_left)[:8]:
                print(f"      x0={x0} ({x0 - lim:+.1f}) {text!r}")
        if not clipped_right and not clipped_left:
            print("    no text overflow")
    print()

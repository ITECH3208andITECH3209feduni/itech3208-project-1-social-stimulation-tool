import multer from "multer";
import path from "path";

const IMAGE_MAX_SIZE = 5 * 1024 * 1024; // 5MB
const VIDEO_MAX_SIZE = 500 * 1024 * 1024; // 500MB
const DEFAULT_MAX_SIZE = 10 * 1024 * 1024; // 10MB

const ALLOWED_IMAGE_TYPES = /jpeg|jpg|png|gif|webp/;
const ALLOWED_IMAGE_MIMES = /image\/jpeg|image\/jpg|image\/png|image\/gif|image\/webp/;

const ALLOWED_VIDEO_TYPES = /mp4|mkv|mov|avi|webm/;
const ALLOWED_VIDEO_MIMES =
    /video\/mp4|video\/x-matroska|video\/quicktime|video\/x-msvideo|video\/webm/;

const fileFilter = (req, file, callback) => {
    const ext = path.extname(file.originalname).toLowerCase().replace(".", "");

    const isImage = ALLOWED_IMAGE_TYPES.test(ext) && ALLOWED_IMAGE_MIMES.test(file.mimetype);

    const isVideo = ALLOWED_VIDEO_TYPES.test(ext) && ALLOWED_VIDEO_MIMES.test(file.mimetype);

    if (!isImage && !isVideo) {
        callback(
            new Error(
                "File type is not supported. Allowed: images (jpg, png, webp, gif) or videos (mp4, mkv, mov, avi, webm)",
            ),
            false,
        );
        return;
    }

    callback(null, true);
};

const storage = multer.memoryStorage();

const multerConfig = multer({
    storage: storage,
    fileFilter: fileFilter,
    limits: {
        fileSize: VIDEO_MAX_SIZE, // Allow up to 500MB (video); specific limits enforced in validation schema
    },
});

export default multerConfig;

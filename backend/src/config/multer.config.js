import multer from "multer";
import path from "path";

const FILE_SIZE = 10 * 1024 * 1024; // 10MB

const fileFilter = (req, file, callback) => {
    const fileTypes = /jpeg|jpg|png|gif/;
    const mimeTypes = /image\/jpeg|image\/jpg|image\/png|image\/gif/;
    const extname = path.extname(file.originalname).toLowerCase();
    const checkFileType = fileTypes.test(extname);
    const checkMimeType = mimeTypes.test(file.mimetype);

    if (!checkFileType) {
        callback(new Error("File type does not supported"), false);
        return;
    }
    if (!checkMimeType) {
        callback(new Error("Mime Type does not supported"), false);
        return;
    }
    callback(null, true);
};

const storage = multer.memoryStorage();

const multerConfig = multer({
    storage: storage,
    fileFilter: fileFilter,
    limits: {
        fileSize: FILE_SIZE,
    },
});

export default multerConfig;

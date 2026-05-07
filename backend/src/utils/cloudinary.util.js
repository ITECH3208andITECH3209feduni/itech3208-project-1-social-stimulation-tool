import streamifier from "streamifier";
import UPLOAD_FOLDERS from "#constants/upload-folder.constant.js";
import { cloudinary } from "#config/cloudinary.config.js";
import uuidUtil from "./uuid.util.js";

const genFileName = ({ prefix, entityId }) => {
    const timestamp = Date.now();

    const uuidV4 = uuidUtil.genV4().split("-")[0].toUpperCase();

    return `${prefix}_${entityId}_${timestamp}_${uuidV4}`;
};

const buildFolderPath = (folder) => {
    return `${UPLOAD_FOLDERS.BASE_FOLDER_DEV}/${folder}`;
};

const uploadBuffer = async ({ fileBuffer, folder, fileName }) => {
    return new Promise((resolve, reject) => {
        try {
            if (!fileBuffer) {
                return reject(new Error("fileBuffer is required"));
            }

            if (!Buffer.isBuffer(fileBuffer)) {
                return reject(new Error("fileBuffer must be Buffer"));
            }

            const fullFolderPath = buildFolderPath(folder);

            const stream = cloudinary.uploader.upload_stream(
                {
                    folder: fullFolderPath,
                    public_id: fileName,
                    overwrite: true,
                    resource_type: "image",
                },
                (error, result) => {
                    if (error) return reject(error);
                    resolve(result);
                },
            );

            const readable = streamifier.createReadStream(fileBuffer);

            readable.on("error", reject);

            readable.pipe(stream);
        } catch (err) {
            reject(err);
        }
    });
};

const uploadVideo = async ({ fileBuffer, folder, fileName }) => {
    return new Promise((resolve, reject) => {
        try {
            if (!fileBuffer) {
                return reject(new Error("fileBuffer is required"));
            }

            if (!Buffer.isBuffer(fileBuffer)) {
                return reject(new Error("fileBuffer must be Buffer"));
            }

            const fullFolderPath = buildFolderPath(folder);

            const stream = cloudinary.uploader.upload_stream(
                {
                    folder: fullFolderPath,
                    public_id: fileName,
                    overwrite: true,
                    resource_type: "video",
                },
                (error, result) => {
                    if (error) return reject(error);
                    resolve(result);
                },
            );

            const readable = streamifier.createReadStream(fileBuffer);

            readable.on("error", reject);

            readable.pipe(stream);
        } catch (err) {
            reject(err);
        }
    });
};

const deleteFile = async (publicId) => {
    return await cloudinary.uploader.destroy(publicId);
};

const cloudinaryUtil = {
    genFileName,
    buildFolderPath,
    uploadBuffer,
    uploadVideo,
    deleteFile,
};

export default cloudinaryUtil;

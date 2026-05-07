import express from "express";
import { bodyMw, authMw, fileMw } from "#middlewares/index.js";
import { multerConfig } from "#config/index.js";
import UserController from "./user.controller.js";
import UserSchema from "./user.validation.js";

const router = express.Router();

router.get(
    "/get-user-infor",
    authMw.authorizeRole([authMw.UserRole.user]),
    UserController.getUserInfor,
);

router.patch(
    "/upload-avatar",
    authMw.authorizeRole([authMw.UserRole.user]),
    multerConfig.single("avatar"),
    fileMw.check,
    fileMw.validate(UserSchema.uploadAvatar),
    UserController.uploadAvatar,
);

export default router;

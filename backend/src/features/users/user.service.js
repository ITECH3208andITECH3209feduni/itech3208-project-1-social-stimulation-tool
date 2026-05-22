import UPLOAD_FOLDERS from "#constants/upload-folder.constant.js";
import { UserModel } from "#models/index.js";
import { bcryptUtil, cloudinaryUtil } from "#utils/index.js";
import UserMessages from "./user.message.js";

const UserService = {
    _formatUser: (user) => {
        if (!user) return null;
        const { _id, password, __v, isDeleted, ...rest } = user._doc || user;
        return {
            id: _id,
            ...rest,
        };
    },

    checkExistedUser: async (payload) => {
        const existedUser = await UserModel.findOne({
            $or: [{ username: payload.username }, { email: payload.email }],
        });

        if (!existedUser) {
            return null;
        }

        if (existedUser.username === payload.username) {
            throw UserMessages.error.USERNAME_IS_EXIST();
        }

        if (existedUser.email === payload.email) {
            throw UserMessages.error.EMAIL_IS_EXIST();
        }

        return existedUser;
    },

    checkCredential: async (payload) => {
        const existedUser = await UserModel.findOne({ username: payload.username });

        if (!existedUser) {
            return null;
        }

        if (existedUser.username != payload.username) {
            throw UserMessages.error.USERNAME_IS_NOT_CORRECT();
        }

        const validPasssword = bcryptUtil.comparePassword(payload.password, existedUser.password);

        if (!validPasssword) {
            throw UserMessages.error.PASSWORD_IS_NOT_CORRECT();
        }

        return existedUser;
    },

    insertUser: async (payload) => {
        const user = await UserModel.create(payload);
        return UserService._formatUser(user);
    },

    getUserInfor: async (id) => {
        const existUser = await UserModel.findById(id);

        if (!existUser) {
            throw UserMessages.error.USER_IS_NOT_EXIST();
        }

        return UserService._formatUser(existUser);
    },

    updateProfile: async ({ userId, payload }) => {
    const existedUser = await UserModel.findById(userId);

    if (!existedUser) {
        throw UserMessages.error.USER_IS_NOT_EXIST();
    }

    // Check username duplicate
    if (
        payload.username &&
        payload.username !== existedUser.username
    ) {
        const usernameExist = await UserModel.findOne({
            username: payload.username,
        });

        if (usernameExist) {
            throw UserMessages.error.USERNAME_IS_EXIST();
        }
    }

    // Check email duplicate
    if (
        payload.email &&
        payload.email !== existedUser.email
    ) {
        const emailExist = await UserModel.findOne({
            email: payload.email,
        });

        if (emailExist) {
            throw UserMessages.error.EMAIL_IS_EXIST();
        }
    }

    existedUser.firstName =
        payload.firstName ?? existedUser.firstName;

    existedUser.lastName =
        payload.lastName ?? existedUser.lastName;

    existedUser.username =
        payload.username ?? existedUser.username;

    existedUser.email =
        payload.email ?? existedUser.email;

    await existedUser.save();

    return UserService._formatUser(existedUser);
},

    uploadAvatar: async ({ userId, file }) => {
        const existedUser = await UserModel.findById(userId);

        if (!existedUser) {
            throw UserMessages.error.USER_IS_NOT_EXIST();
        }

        if (existedUser.avatar?.cloudinaryId) {
            await cloudinaryUtil.deleteFile(existedUser.avatar?.cloudinaryId);
        }

        const fileName = cloudinaryUtil.genFileName({
            prefix: "avatar",
            entityId: existedUser._id,
        });

        const uploadedAvatar = await cloudinaryUtil.uploadBuffer({
            fileBuffer: file.buffer,
            folder: UPLOAD_FOLDERS.USERS.AVATARS,
            fileName: fileName,
        });

        existedUser.avatar = {
            url: uploadedAvatar.secure_url,
            cloudinaryId: uploadedAvatar.public_id,
        };

        await existedUser.save();

        return UserService._formatUser(existedUser);
    },
};

export default UserService;

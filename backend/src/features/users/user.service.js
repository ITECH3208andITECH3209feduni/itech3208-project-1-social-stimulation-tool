import { UserModel } from "#models/index.js";
import { bcryptUtil } from "#utils/index.js";
import UserMessages from "./user.message.js";

const UserService = {
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
        return await UserModel.create(payload);
    },

    getUserInfor: async (id) => {
        const existUser = await UserModel.findById(id);

        if (!existUser) {
            throw UserMessages.error.USER_IS_NOT_EXIST();
        }

        const { password, __v, ...cleanedUser } = existUser._doc;

        return cleanedUser;
    },
};

export default UserService;

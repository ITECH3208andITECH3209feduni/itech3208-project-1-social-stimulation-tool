import { LevelModel } from "#models/index.js";
import LevelMessages from "./level.message.js";

const LevelService = {
    _formatLevel: (level) => {
        if (!level) return null;
        const { _id, __v, isDeleted, ...rest } = level._doc || level;
        return {
            id: _id,
            ...rest,
        };
    },

    insertLevel: async (name) => {
        const exist = await LevelModel.findOne({ name });

        if (exist) {
            throw LevelMessages.error.TUTORIAL_LEVEL_IS_EXIST();
        }

        const level = await LevelModel.create({ name });
        return LevelService._formatLevel(level);
    },

    bulkInsertLevels: async () => {
        const count = await LevelModel.countDocuments();
        const isEmpty = count == 0;

        if (!isEmpty) {
            throw LevelMessages.error.LIST_TUTORIAL_LEVELS_EXIST();
        }

        const levels = [{ name: "Beginner" }, { name: "Intermediate" }, { name: "Advanced" }];

        const inserted = await LevelModel.insertMany(levels);
        return inserted.map(LevelService._formatLevel);
    },

    getLevels: async () => {
        const levels = await LevelModel.find().lean();

        if (!levels || levels.length == 0) {
            throw LevelMessages.error.LIST_TUTORIAL_LEVELS_IS_EMPTY();
        }

        return {
            total: levels.length,
            levels: levels.map(LevelService._formatLevel),
        };
    },
};

export default LevelService;

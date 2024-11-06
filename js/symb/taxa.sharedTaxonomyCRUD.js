export const standardizeCultivarEpithet = (unstandardizedCultivarEpithet) => {
  if (unstandardizedCultivarEpithet) {
    const cleanString = unstandardizedCultivarEpithet.replace(
      "/(^[\"'“]+)|([\"'”]+$)/",
      ""
    );
    return "'" + cleanString + "'";
  } else {
    return "";
  }
};

export const standardizeTradeName = (unstandardizedTradeName) => {
  if (unstandardizedTradeName) {
    return unstandardizedTradeName.toUpperCase();
  } else {
    return "";
  }
};

export const debounce = (func, delay) => {
  // thanks for the idea, chatGtp!
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
};

export const rankIdsToHideUnit2From = {
  "non-ranked node": 0,
  organism: 1,
  kingdom: 10,
  subkingdom: 20,
  division: 30,
  subdivision: 40,
  superclass: 50,
  class: 60,
  subclass: 70,
  order: 100,
  suborder: 110,
  family: 140,
  subfamily: 150,
  tribe: 160,
  subtribe: 170,
  genus: 180,
  subgenus: 190,
  section: 200,
  subsection: 210,
};
export const { ...rest } = rankIdsToHideUnit2From;
export const rankIdsToHideUnit3From = { ...rest, species: 220 };
export const { ...rest2 } = rankIdsToHideUnit3From;

export const rankIdsToHideUnit4From = {
  ...rest2,
  subspecies: 230,
  variety: 240,
  subvariety: 250,
  form: 260,
  subform: 270,
};
export const { ...rest3 } = rankIdsToHideUnit4From;
export const rankIdsToHideUnit5From = { ...rest3 };

export const allRankIds = { ...rest3, cultivar: 300 };

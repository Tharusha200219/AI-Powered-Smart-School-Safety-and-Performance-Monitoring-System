import { useState, useEffect } from 'react';

const useLanguage = () => {
  const [lang, setLang] = useState('en');

  useEffect(() => {
    // Logic to load language from localStorage or default to 'en'
    const savedLang = localStorage.getItem('language') || 'en';
    setLang(savedLang);
  }, []);

  const changeLanguage = (newLang) => {
    setLang(newLang);
    localStorage.setItem('language', newLang);
  };

  return { lang, changeLanguage };
};

export default useLanguage;
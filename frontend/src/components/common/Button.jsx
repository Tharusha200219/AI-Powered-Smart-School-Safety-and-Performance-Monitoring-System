import PropTypes from 'prop-types';

const Button = ({ text, color = 'blue', onClick }) => {
  const baseStyles = 'px-4 py-2 rounded font-semibold text-white transition';
  const colorStyles = {
    blue: 'bg-blue-600 hover:bg-blue-700',
    red: 'bg-red-600 hover:bg-red-700',
    green: 'bg-green-600 hover:bg-green-700',
  };

  return (
    <button className={`${baseStyles} ${colorStyles[color]}`} onClick={onClick}>
      {text}
    </button>
  );
};

Button.propTypes = {
  text: PropTypes.string.isRequired,
  color: PropTypes.oneOf(['blue', 'red', 'green']),
  onClick: PropTypes.func,
};

export default Button;
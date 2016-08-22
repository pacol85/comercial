var Titulo = React.createClass({
  render: function() {
    return (
      React.createElement('h1', null, this.props.t)
    );
  }
});
ReactDOM.render(
  <Titulo t="Par&aacute;metros del Sistema" />,
  document.getElementById('titulo')
);
